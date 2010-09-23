<?php

function render_ioMenu($name = null)
{
  return get_ioMenu($name)->render();
}

function get_ioMenu($name = null)
{
  $ioMenus = include(sfContext::getInstance()->getConfigCache()->checkConfig('config/navigation.yml'));
  $menu = ioMenu::createFromArray($ioMenus[$name]);
  return $menu;
}
