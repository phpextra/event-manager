<?php

namespace PHPExtra\EventManager\Worker;

use PHPExtra\Sorter\Comparator\NumericComparator;
use PHPExtra\Sorter\SorterInterface;
use PHPExtra\Sorter\Strategy\ComplexSortStrategy;
use PHPExtra\Type\Collection\Collection;

/**
 * The SortableWorkerQueue class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class SortableWorkerQueue extends Collection implements WorkerQueueInterface
{
    /**
     * @var \SplObjectStorage
     */
    private $objectWeights;

    /**
     * @var int
     */
    private $weight = 0;

    /**
     * @var SorterInterface
     */
    private $sorter;

    /**
     * @param array|WorkerInterface[] $entities
     */
    public function __construct(array $entities = array())
    {
        $this->objectWeights = new \SplObjectStorage();
        parent::__construct($entities);
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkers()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addWorker(WorkerInterface $worker)
    {
        $this->add($worker);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function add($entity)
    {
        if(!$entity instanceof WorkerInterface){

            if(is_object($entity)){
                $type = get_class($entity);
            }else{
                $type = gettype($entity);
            }

            throw new \RuntimeException(sprintf('Given value (%s) must be an instance of a class implementing WorkerInterface', $type));
        }

        $this->objectWeights->attach($entity, $this->weight);
        $this->weight++;

        parent::add($entity);

        $this->sort($this->getSorter());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function sort(SorterInterface $sorter)
    {
        $this->rewind();
        $this->entities = $sorter->sort($this->entities);
        return $this;
    }

    /**
     * @return SorterInterface
     */
    protected function getSorter()
    {
        if(!$this->sorter){
            $sorter = new ComplexSortStrategy();
            $sorter->setComparator(new NumericComparator());
            $comparator = new NumericComparator();

            $objectWeights = $this->objectWeights;

            $sorter
                ->sortBy(function(WorkerInterface $worker){
                    return (int)$worker->getPriority();
                }, SorterInterface::DESC, $comparator)
                ->sortBy(function(WorkerInterface $worker) use ($objectWeights){
                    return $objectWeights->offsetGet($worker);
                }, SorterInterface::DESC, $comparator)
            ;

            $this->sorter = $sorter;
        }

        return $this->sorter;
    }
}