<?php namespace Skovachev\Builder;

use App;
use Illuminate\Support\Collection;

class BuilderService
{
    protected $context = array();

    public function addContextData($key, $value)
    {
        $this->context[$key] = $value;
    }

    public function buildResponse($data)
    {
        if ($data instanceof Collection)
        {
            $that = $this;
            $data = $data->map(function($item) use ($that)
            {
                return $that->buildObject($item);
            });
        }

        return $this->buildObject($data);
    }

    public function buildObject($item)
    {
        if ($item instanceof Buildable)
        {
            $builderClass = $item->getBuilderClass();
            $builder = App::make($builderClass);
            $builder->setContext($this->context);
            return $builder->build($item);
        }
        else if (is_object($item) && method_exists($item, 'toArray'))
        {
            return $item->toArray();
        }
        else if (is_array($item) || is_string($item))
        {
            return $item;
        }

        throw new BuildException;
    }
}