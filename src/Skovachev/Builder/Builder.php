<?php namespace Skovachev\Builder;

use App;
use Str;

abstract class Builder
{
    protected $attributes = array();
    protected $relations = array();

    private $context = array();

    public function setContext($context = array())
    {
        $this->context = $context;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->context)) {
            return $this->context[$key];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $key .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);

        return null;
    }

    /**
     * @return array representation of object
     */
    public function build(Buildable $object)
    {
        $built = array();

        $attributes = $this->parseItems('attributes');

        foreach ($attributes as $alias => $attribute) {
            if (isset($object->$attribute))
            {
                $built[$alias] = $object->$attribute;
            }
        }

        $objectRelations = $object->relationsToArray();
        $relations = $this->parseItems('relations');

        foreach ($relations as $alias => $relation) {
            if (isset($objectRelations[$relation]))
            {
                $relationData = $object->getRelation(Str::camel($relation));
                $builderService = App::make('builder_service');
                $built[$alias] = $builderService->buildResponse($relationData);
            }
        }

        return $built;
    }

    protected function parseItems($itemsKey)
    {
        $items = array();

        foreach ($this->$itemsKey as $key => $name) {
            if (is_numeric($key))
            {
                $items[$name] = $name;
            }
            else
            {
                $items[$key] = $name;
            }
        }

        return $items;
    }
}