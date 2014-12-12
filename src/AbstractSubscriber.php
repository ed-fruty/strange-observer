<?php
namespace Fruty\Observe;

/**
 * Class AbstractSubscriber
 * @package Fruty\Observe
 * @author Fruty <ed.fruty@gmail.com>
 */
abstract class AbstractSubscriber
{
    /**
     * Get methods priorities
     * For non-defined actions default priority will set to Event::PRIORITY_NORMAL
     *
     * @example return array('onBeforeAction' => Event::PRIORITY_LOW, 'onAfterAction' => Event::PRIORITY_HIGH);
     * @return array
     */
    public static function getPriorities()
    {
        return array();
    }
} 