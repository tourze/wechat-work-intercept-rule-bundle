<?php

namespace WechatWorkInterceptRuleBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum InterceptType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case WARN = '1';
    case NOTICE = '2';

    public function getLabel(): string
    {
        return match ($this) {
            self::WARN => '警告并拦截发送',
            self::NOTICE => '仅发警告',
        };
    }
}
