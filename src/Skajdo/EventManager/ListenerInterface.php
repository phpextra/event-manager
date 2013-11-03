<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * Abstract listener class
 * Listener does not require any specific methods. Any method with event as it first param is treated
 * as listener and it will be executed as soon as event will be triggered.
 *
 * @author      Jacek Kobus
 */
interface ListenerInterface
{
}