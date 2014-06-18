<?php

namespace Bryder\Events\TypoScript;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;


class SortByDateOperation extends AbstractOperation {

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    static protected $shortName = 'sortByDate';

    /**
     * {@inheritdoc}
     *
     * @param array (or array-like object) $context onto which this operation should be applied
     * @return boolean TRUE if the operation can be applied onto the $context, FALSE otherwise
     */
    public function canEvaluate($context) {
        return (isset($context[0]));
    }

    /**
     * Evaluate the operation on the objects inside $flowQuery->getContext(),
     * taking the $arguments into account.
     *
     * The resulting operation results should be stored using $flowQuery->setContext().
     *
     * If the operation is final, evaluate should directly return the operation result.
     *
     * @param \TYPO3\Eel\FlowQuery\FlowQuery $flowQuery the FlowQuery object
     * @param array $arguments the arguments for this operation
     * @return mixed|null if the operation is final, the return value
     */
    public function evaluate(\TYPO3\Eel\FlowQuery\FlowQuery $flowQuery, array $arguments) {
        $context = $flowQuery->getContext();

        //remove items with no date - we can't sort these
        foreach ($context as $key=>$value) {
            if (!method_exists($value->getProperty($arguments[0]), 'getTimestamp')) {
                unset($context[$key]);
            }
        }

        usort($context, function ($a, $b) use ($arguments){
            if ($a->getProperty($arguments[0])->getTimestamp() == $b->getProperty($arguments[0])->getTimestamp()) {
                return 0;
            }
            if (isset($arguments[1]) && $arguments[1] == 'ascending') {
                return ($a->getProperty($arguments[0])->getTimestamp() < $b->getProperty($arguments[0])->getTimestamp()) ? -1 : 1;
            } else {
                return ($a->getProperty($arguments[0])->getTimestamp() < $b->getProperty($arguments[0])->getTimestamp()) ? 1 : -1;
            }
        });

        if ((isset($arguments[2]) && $arguments[2] == 'upcoming')) {
            $now = new \DateTime();
            //Neos 1.0.2 fix - time not persisted in db
            $now->sub(new \DateInterval('P1D'));
            foreach ($context as $key=>$value) {
                if ($now->getTimestamp() > $value->getProperty($arguments[0])->getTimestamp()) {
                    unset($context[$key]);
                }
            }
        }
        return $flowQuery->setContext($context);
    }

}