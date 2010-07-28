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
}

