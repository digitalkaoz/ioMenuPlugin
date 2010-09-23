<?php

/**
 * caches the ioMenus defined in the navigation.yml
 *   - you can infinite nest the menus
 *   - it has a fluent interface to ioMenu::createFromArray
 *   - seamless fetching of security settings from security.yml
 *
 * @package     ioMenuPlugin
 * @subpackage  config
 * @author      Robert Schönthal <seroscho@googlemail.com>
 * @see         navigation.sample.yml for configuration hints
 */
class ioMenuConfigHandler extends sfYamlConfigHandler
{
  /**
   * holds the config cache buffer
   *
   * @var string
   */
  private $buffer = "<?php\n
return array(\n";

  /**
   * holds the menu instances
   *
   * @var array
   */
  public $menus = array();

  /**
   * the sfContext
   *
   * @var sfContext
   */
  private $context;

  /**
   * security configuration
   *
   * @var array
   */
  protected $security;

  /**
   * executes the config files
   *
   * @param array $configFiles
   */
  public function execute($configFiles)
  {
    $config = $this->parseYamls($configFiles);
    
    $this->setContext();

    if (!$config)
    {
      return false;
    }

    $this->iterateMenus($config);

    return $this->buffer;
  }

  /**
   * iterate over the defined menus
   *
   * @param array $config
   */
  protected function iterateMenus(&$config)
  {
    if(is_array($config))
    {
      array_walk($config, array($this, 'parseMenu'));
    }
    else
    {
      $this->parseMenu($config, 'menu');
    }
    $this->buffer .= ");\n";
  }

  /**
   * parses a menu configuration
   *
   * @param array $menu
   * @param string $name
   */
  protected function parseMenu(&$menu, $name)
  {
    $this->menus[$name] = $menu;

    if(!isset($menu['name']))
    {
      $menu['name'] = $name;
    }

    array_walk($menu['children'], array($this,'parseItem'));

    $this->buffer .= sprintf("// menu '%1\$s'\n '%1\$s' => %2\$s,\n", $name, var_export($menu, true));
  }

  /**
   * parses a menu item
   *
   * @param array $item
   * @param string $name
   * @param string $menu
   * @param array $root
   * 
   * @todo too many parameters
   */
  protected function parseItem(&$data, $key)
  {
    if(isset($data['route']))
    {
      $this->setSecuritySettingsForItem($data);
    }

    if(!isset($data['name']))
    {
      $data['name'] = $key;
    }

    if(isset($data['children']) && is_array($data['children']) && !empty($data['children']))
    {
      array_walk($data['children'], array($this,'parseItem'));
    }
  }

  /**
   * sets the sfContext
   *
   * @param sfContext $context
   */
  public function setContext(sfContext $context=null)
  {
    $this->context = $context ? $context : sfContext::getInstance();
  }

  /**
   * get the security settings for a route
   *
   * @param sfRoute $route
   * @return array
   */
  protected function getSecurityConfigForRoute(sfRoute $route)
  {
    $route_defaults = $route->getDefaults() ? $route->getDefaults() : $route->getDefaultParameters();
    $config = $this->context->getConfiguration();

    if ($file = $config->getConfigCache()->checkConfig('modules/'.$route_defaults['module'].'/config/security.yml', true))
    {
      require $file;
    }
    else
    {
      $this->security = array();
    }

    $secure = $this->getSecurityValue($route_defaults['action'], 'is_secure');
    $credentials = $this->getSecurityValue($route_defaults['action'], 'credentials');
    
    if (!is_null($credentials) && !is_array($credentials))
    {
      $credentials = array($credentials);
    }

    return array('is_secure' => $secure, 'credentials' => $credentials);
  }

  /**
   * Returns the security configuration for an action
   *
   * @param string $action   The action name
   * @param string $name     The name of the value to pull from security.yml
   * @param mixed  $default  The default value to return if none is found in security.yml
   *
   * @return mixed
   */
  public function getSecurityValue($action, $name, $default = null)
  {
    $actionName = strtolower($action);

    if (isset($this->security[$actionName][$name]))
    {
      return $this->security[$actionName][$name];
    }

    if (isset($this->security['all'][$name]))
    {
      return $this->security['all'][$name];
    }

    return $default;
  }

  /**
   * extracts the sfRoute from the item if exists
   *
   * @param array $item
   * @return mixed
   */
  protected function getRouteFromItem(&$item)
  {
    $config = $this->context->getConfiguration();
    $routing = $this->context->getRouting();
    $routeName = $item['route'];
    $routeName = self::replaceConstants($routeName);
    $item['route'] = $routeName;

    if(strpos($item['route'],'://') || strpos($item['route'],'ww.') || strpos($item['route'],'#') !== false){
      return false;
    }
    elseif(strpos($routeName,'/'))
    {
      $config = $routing->parse($routeName);
      return $config['_sf_route'];
    }
    else
    {
      $routeName  = str_replace('@', '', $routeName);
      
      if(strpos($routeName, '?'))
      {
        $routeName = substr($routeName, 0, strpos($routeName, '?'));
      }

      $routes = $routing->getRoutes();
      return isset($routes[$routeName]) ? $routes[$routeName] : false;
    }

  }

  /**
   * set the security settings for an item
   *
   * @param array $item
   * @return mixed
   */
  protected function setSecuritySettingsForItem(&$item)
  {
    $route = $this->getRouteFromItem($item);

    if(!$route)
    {
      return array();
    }

    $security = $this->getSecurityConfigForRoute($route);

    if(isset($security['credentials']))
    {
      $item['credentials'] = isset($item['credentials']) ? $item['credentials'] : $security['credentials'];
    }

    if($security['is_secure'])
    {
      $item['requires_auth'] = isset($item['requires_auth']) ? $item['requires_auth'] : true;
    }
  }

}
