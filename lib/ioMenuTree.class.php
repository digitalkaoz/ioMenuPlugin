<?php

/**
 * Contains properties and functionality global to entire menu tree.
 *
 * @package     ioMenuPlugin
 * @subpackage  menu
 * @author      Michał Górny <g21.michal@gmail.com>
 * @copyright   Iostudio, LLC 2010
 * @since       2010-07-21
 * @version     svn:$Id$ $Author$
 */
class ioMenuTree
{
  /**
   * Root item for menu tree
   */
  protected $_rootItem = null;

  /**
   * Properties of menu tree
   */
  protected
    $_renderer            = null,    // renderer which is used to render menu items
    $_currentUri          = null,    // the current uri to use for selecting current menu
    $_currentRoute        = null,    // the current route to use for selecting current menu
    $_currentItem         = null,    // current item
    $_culture             = null;    // the culture to use when rendering menu tree

  /**
   * Creates new menu tree for menu item
   * @param ioMenuItem $item
   */
  public function __construct(ioMenuItem $item)
  {
    $this->_rootItem = $item;
  }

  /**
   * Copies menu tree for another menu item
   * @param ioMenuItem $item
   */
  public function copy(ioMenuItem $item)
  {
    $newMenu = clone($this);
    $newMenu->_rootItem = $item;

    return $newMenu;
  }

  /**
   * Returns root item for menu tree
   *
   * @return ioMenuItem root item
   */
  public function getRootItem()
  {
    return $this->_rootItem;
  }

  /**
   * Sets renderer which will be used to render menu items.
   *
   * @param ioMenuItemRenderer $renderer Renderer.
   */
  public function setRenderer(ioMenuItemRenderer $renderer)
  {
    $this->_renderer = $renderer;
  }

  /**
   * Gets renderer which is used to render menu items.
   *
   * @return ioMenuItemRenderer $renderer Renderer.
   */
  public function getRenderer()
  {
    if ($this->_renderer == null) // creates default renderer
    {
      $this->_renderer = new ioMenuItemListRenderer();
    }

    return $this->_renderer;
  }

  /**
   * Returns the current uri, which is used for determining the current
   * menu item.
   *
   * If the uri isn't set, its taken it from the request object.
   *
   * @return string
   */
  public function getCurrentUri()
  {
    if ($this->_currentUri === null)
    {
      $uri = explode('?', sfContext::getInstance()->getRequest()->getUri());
      $this->setCurrentUri($uri[0]);
    }

    return $this->_currentUri;
  }

  /**
   * Sets the current uri, used when determining the current menu item
   *
   * @return void
   */
  public function setCurrentUri($uri)
  {
    $this->_currentUri = $uri;
  }

  /**
   * Returns the current route, which is used for determining the current
   * menu item.
   *
   * If the uri isn't set, its taken it from the request object.
   *
   * @return string
   */
  public function getCurrentRoute()
  {
    if ($this->_currentRoute == null)
    {
      $this->setCurrentRoute(sfContext::getInstance()->getRouting()->getCurrentRouteName());
    }

    return $this->_currentRoute;
  }

  /**
   * Sets the current route, used when determining the current menu item
   *
   * @return void
   */
  public function setCurrentRoute($route)
  {
    $this->_currentRoute = $route;
  }

  /**
   * Returns the current item.
   *
   * @return ioMenuItem current item
   */
  public function getCurrentItem()
  {
    if ($this->_currentItem === null)
    {
      $this->setCurrentItem($this->findCurrentItem($this->_rootItem));
    }

    return $this->_currentItem;
  }

  /**
   * Sets the current item.
   *
   * If you set current item to null, it will be automatically found at
   * first getCurrentItem call.
   *
   * @param ioMenuItem|null $item current item or null
   * @return void
   */
  public function setCurrentItem($item)
  {
    $this->_currentItem = $item;
  }

  /**
   * Recursively finds current item.
   *
   * @param ioMenuItem $item Item to start from
   * @return ioMenuItem current item or null
   */
  protected function findCurrentItem(ioMenuItem $item)
  {
    if ($item->matchCurrentLocation($this))
    {
      return $item;
    }

    foreach($item->getChildren() as $child)
    {
      $current = $this->findCurrentItem($child);
      if ($current)
      {
        return $current;
      }
    }

    return null;
  }

  /**
   * Returns the culture with which this menu tree should render.
   *
   * If the culture has not been set, it will attempt to ask sfContext
   * for a culture. If all else fails, the default culture is returned.
   *
   * @return string
   */
  public function getCulture()
  {
    // if the culture is set, simply return it
    if ($this->_culture !== null)
    {
      return $this->_culture;
    }

    // get it from the context or return the default
    if (sfContext::hasInstance())
    {
      return sfContext::getInstance()->getUser()->getCulture();
    }
    else
    {
      return sfConfig::get('sf_default_culture');
    }
  }

  /**
   * Set the culture that should be used when rendering the menu tree
   *
   * @param  string $culture The culture to use when rendering the menu tree
   * @return void
   */
  public function setCulture($culture)
  {
    $this->_culture = $culture;
  }
}

