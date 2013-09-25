<?php namespace Skovachev\Builder;

use App;
use Str;

abstract class Builder
{
    protected $attributes = array();
    protected $relations = array();

    /**
     * @return array representation of object
     */
    public function build(Buildable $object, $context = array())
    {
        $built = array();

        foreach ($this->attributes as $alias => $attribute) {
            if (isset($object->$attribute))
            {
                $built[$alias] = $object->$attribute;
            }
        }

        $objectRelations = $object->relationsToArray();

        foreach ($this->relations as $alias => $relation) {
            if (isset($objectRelations[$relation]))
            {
                $relationData = $object->getRelation(Str::camel($relation));
                $builderService = App::make('builder_service');
                $built[$alias] = $builderService->buildResponse($relationData);
            }
        }

        return $built;
    }
}