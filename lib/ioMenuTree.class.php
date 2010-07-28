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
    $_renderer         = null,    // renderer which is used to render menu items
    $_currentUri       = null;    // the current uri to use for selecting current menu

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
        $this->setCurrentUri(sfContext::getInstance()->getRequest()->getUri());
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
}

