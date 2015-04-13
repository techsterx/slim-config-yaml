<?php

namespace BurningDiode\Slim\Config;

class ParameterBag
{
	protected $parameters;
	protected $resolved;

	public function __construct(array $parameters = array())
	{
		$this->parameters = array();
		$this->add($parameters);
		$this->resolved = false;
	}

	public function clear()
	{
		$this->parameters = array();
	}

	public function add(array $parameters)
	{
		foreach ($parameters as $key => $value) {
			$this->parameters[strtolower($key)] = $value;
		}
	}

	public function get($name)
	{
		$name = strtolower($name);

		if (!array_key_exists($name, $this->parameters)) {
			throw new \Exception('Parameter ' . $name . ' not found.');
		}

		return $this->parameters[$name];
	}

	public function set($name, $value)
	{
		$this->parameters[strtolower($name)] = $value;
	}

	public function has($name)
	{
		return array_key_exists(strtolower($name), $this->parameters);
	}

	public function remove($name)
	{
		unset($this->parameters[strtolower($name)]);
	}

	public function resolve()
	{
		if ($this->resolved) {
			return;
		}

		$parameters = array();

		foreach ($this->parameters as $key => $value) {
			try {
				$value = $this->resolveValue($value);
				$parameters[$key] = $this->unescapeValue($value);
			} catch (\Exception $e) {
				//$e->setSourceKey($key);

				throw $e;
			}
		}

		$this->parameters = $parameters;
		$this->resolved = true;
	}

	public function resolveValue($value, array $resolving = array())
	{
		if (is_array($value)) {
			$args = array();

			foreach ($value as $k => $v) {
				$args[$this->resolveValue($k, $resolving)] = $this->resolveValue($v, $resolving);
			}

			return $args;
		}

		if (!is_string($value)) {
			return $value;
		}

		return $this->resolveString($value, $resolving);
	}

	public function resolveString($value, array $resolving = array())
	{
		if (preg_match('/^%([^%\s]+)%$/', $value, $match)) {
			$key = strtolower($match[1]);

			if (isset($resolving[$key])) {
				throw new \Exception('Parameters Circular Reference: ' . $key);
			}

			$resolving[$key] = true;

			return $this->resolved ? $this->get($key) : $this->resolvevalue($this->get($key), $resolving);
		}

		$self = $this;

		return preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($self, $resolving, $value) {
			if (!isset($match[1])) {
				return '%%';
			}

			$key = strtolower($match[1]);

			if (isset($resolving[$key])) {
				throw new \Exception('Parameters Circular Reference: ' . $key);
			}

			$resolved = $self->get($key);

			if (!is_string($resolved) && !is_numeric($resolved)) {
				throw new \Exception(sprintf('A string value must be composed of strings and/or numbers, but found parameter "%s" of type %s inside string value "%s".', $key, gettype($resolved), $value));
			}

			$resolved = (string) $resolved;
			$resolving[$key] = true;

			return $self->isResolved() ? $resolved : $self->resolveString($resolved, $resolving);
		}, $value);
	}

	public function isResolved()
	{
		return $this->resolved;
	}

	public function escapeValue($value, $unescape = false)
	{
		$characters = array(array('%', '@'), array('%%', '@@'));

		if ($unescape === true) {
			$characters = array_reverse($characters);
		}

		list($search, $replace) = $characters;

		return json_decode(str_replace($search, $replace, json_encode($value)), true);
	}

	public function unescapeValue($value)
	{
		return $this->escapeValue($value, true);
	}
}
