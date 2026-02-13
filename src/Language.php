<?php

namespace Omisai\Szamlazzhu;

enum Language: string
{
    case HU = 'hu';

    case EN = 'en';

    case DE = 'de';

    case IT = 'it';

    case RO = 'ro';

    case SK = 'sk';

    case HR = 'hr';

    case FR = 'fr';

    case ES = 'es';

    case CZ = 'cz';

    case PL = 'pl';

    case BG = 'bg';

    case NL = 'nl';

    case RU = 'ru';

    case SI = 'si';

    public static function getDefault(): self
    {
        return self::HU;
    }

    public static function getLanguageName(Currency $language): string
    {
        switch ($language) {
            case self::HU:
                $result = 'magyar';
                break;
            case self::EN:
                $result = 'angol';
                break;
            case self::DE:
                $result = 'német';
                break;
            case self::IT:
                $result = 'olasz';
                break;
            case self::RO:
                $result = 'román';
                break;
            case self::SK:
                $result = 'szlovák';
                break;
            case self::HR:
                $result = 'horvát';
                break;
            case self::FR:
                $result = 'francia';
                break;
            case self::ES:
                $result = 'spanyol';
                break;
            case self::CZ:
                $result = 'cseh';
                break;
            case self::PL:
                $result = 'lengyel';
                break;
            default:
                $result = 'ismeretlen';
                break;
        }

        return $result;
    }
}
