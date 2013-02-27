<?php

/**
 * Priority queue.
 *
 * Fixes issues with default Queue. Iterations may be slow as inner iterator is created.
 * It was made due to bug-feature (?) that causes queue elements to be removed.
 *
 * If using Zend, it has its own SplPriorityQueue replacement if PHP < 5.3
 * as it's avilable only in 5.3 and above.
 *
 * @author      Jacek Kobus
 * @category    App
 * @package     App_EventManager
 */
class Queue extends App_Util_PriorityQueue
{}