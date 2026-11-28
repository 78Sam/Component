
<?php

declare(strict_types=1);

namespace ;

use Core\Components\Models\Schematics\AbstractSchematic;

/**
 * @see ComponentPHP/App/Components/HTML/Users/user_card.html
 */
final class UserCardSchematic extends AbstractSchematic
{
    
public const string _CLASS = 'class';

public const string _COMPONENT_CONSTANT = 'componentConstant';

public const string _USER_CARD = 'userCard';


    protected static function getRawPath(): string
    {
        return 'App/Components/HTML/Users/user_card.html';
    }
}
