<?php

namespace Oxygen\Crud;

use Oxygen\Core\View\Factory;

/**
 * Ensures that the input is well-formed and valid Blade/raw PHP code.
 * WARNING: this is hilariously insecure as it is executing raw user input
 *          this is only somewhat mitigated by the fact that only administrators who
 *          would have full server access anyway, should be allowed to update content fields.
 */
class BladeTemplateValidator {

    /**
     * The view factory compiles and renders the view, throwing an exception if there is an error in it.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Stores the last exception that was thrown while validating.
     * Because the code within Laravel's Validator class runs synchronously,
     * this should always be corresponding to the last field that was `validate`d
     *
     * @var \Exception
     */
    protected $lastExceptionThrown;

    /**
     * Constructs the validator
     *
     * @param Factory $factory the view factory used to compile and render views
     */
    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * Validates the code, by compiling it, and executing it,
     * and ensuring it doesn't throw any exceptions.
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @param \Illuminate\Validation\Validator $validator
     */
    public function validate($attribute, $value, $parameters, $validator) {
        //$path = $factory->pathFromModel(get_class($page), $page->getId(), 'content');

        try {
            $this->factory->string($value, hash("sha256", $value), 0)->render();
            return true;
        } catch(\Exception $e) {
            $this->lastExceptionThrown = $e;
            return false;
        }
    }

    /**
     * Replaces the bland validation message with more important information about
     * the exception that occured.
     *
     * @param string $message the original message
     * @param string $attribute
     * @param string $rule
     * @param array $parameters
     * @return string the new message
     */
    public function replace($message, $attribute, $rule, $parameters) {
        // trim off the (View: xxx) cruft wrapping the original exception
        $e = $this->lastExceptionThrown->getPrevious();
        // make replacements
        $message = str_replace(':exception.message', $e->getMessage(), $message);
        $message = str_replace(':exception.line', $e->getLine(), $message);
        $message = str_replace(':exception.file', $e->getFile(), $message);
        $reflect = new \ReflectionClass($e);
        $message = str_replace(':exception.shortClassName', $reflect->getShortName(), $message);
        $message = str_replace(':exception.className', $reflect->getName(), $message);
        return $message;
    }

}
