The `tree` property
=============

Every menu item contains tree property, which is special object for working
with the menu tree as a whole. The tree allows you to set properties global to
the entire tree. For example:

    $menu = new ioMenuItem('My menu');
    $menu->addChild('overview', '@homepage');
    $menu->addChild('comments', '@comments');

    $menu->getTree()->setCurrentUri('@homepage');

Every item in a menu tree shares same tree object. For example, the following
code would be redundant, as the current uri is being set repeatedly on the same
object.

    // same effects
    $menu->getTree()->setCurrentUri('@homepage');
    $menu['overview']->getTree()->setCurrentUri('@homepage');
    $menu['comments']->getTree()->setCurrentUri('@homepage');

Removing item(s) from the menu tree
---------------

Each time you remove a menu item from a parent menu, that menu item will get a
new tree object, which will inherit properties of its parent's tree object.
In other words, if you split a menu tree into pieces, each menu tree will have a
distinct tree object that resembles the original tree object.

Consider the following example:

    $menu = new ioMenuItem('My menu');
    $menu->addChild('overview', '@homepage');
    $menu->addChild('comments', '@comments');
    $menu['comments']->addChild('recent', '@comments_recent');

    $menu->getTree()->setCurrentUri('@homepage');

Now, let's create a new, independent menu tree by removing the `comments` menu
from the main menu tree:

    $commentsMenu = $menu['comments'];
    $menu->removeChild($comments);

You now have two distinct menu trees (`$menu` and `$commentsMenu`). Those two
menu trees will each have their own tree object.

Because `$commentsMenu` was created by removing it from `$menu`, its tree object
inherits properties of the `$menu` tree object:

    // returns @homepage
    $commentsMenu->getTree()->getCurrentUri();

Adding item(s) to a menu tree
---------------

Each time you add a menu item to a parent menu item, the new item will receive
the tree object from its parent.

Let's continue working on the previous example:

    $menu->getTree()->setCurrentUri('@homepage');
    $commentsMenu->getTree()->setCurrentUri('');

Now let's again add $commentsMenu to $menu:

    $menu->addChild($commentsMenu);

The tree object for `$commentsMenu` is replaced by the tree object from `$menu`.
Each menu item once again shares the same tree object:

    // returns @homepage
    $commentsMenu->getTree()->getCurrentUri();

