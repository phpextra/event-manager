4.0.0

- redesigned external interfaces
- removed deprecated methods
- optimized listener queue
- added more tests to ensure proper worker execution order
- added service provider for [Silex](http://silex.sensiolabs.org/) (supports [silex web profiler](https://github.com/silexphp/Silex-WebProfiler))

3.0.0

**This version is broken and will not be maintained.**

2.1.0

- added SplPriorityQueue fixing wrong listener execution order

2.0.0

- Fixed problem that caused phpDoc priorities to have wrong values
- Removed WorkerQueueInterface::getWorkers
- Worker factory now assigns an unique Id for each created worker
- Updated log messages sent from event manager
- Changed signature of EventManagerAware (**BC break**)
- Fixed manager trying to call private methods if its first param was implementing the Event

1.0.2

- Modified WorkerQueue - now returns workers in LIFO order if priority of workers is equal
- Removed Zend Priority queue dependency

1.0.1

- Changed MONITOR priority value to ~PHP_INT_MAX
- Removed final keyword form Priority class

1.0.0

- First release