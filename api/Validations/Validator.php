<?php
namespace Warehouse\Validations;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
class Validator
{
    private static $errors = [];
// A generic validation method. It returns true on success; an array of errors on false.
    public static function validate($request, array $rules)
    {
        foreach ($rules as $field => $rule) {
// Retrieve a parameter from url or the request body; id can be sent in url or body.
            $param = $request->getAttribute($field) ?? $request->getParam($field);
try {
    $rule->setName(ucfirst($field))->assert($param);
} catch (NestedValidationException $ex) {
    self::$errors[$field] = $ex->getMessages();
}
}
        return empty(self::$errors);
    }
// Validate attributes of a User model. Do not include fields having default values (id, role, etc.)
    public function validateUser($request)
    {
        $rules = [
            'username' => v::noWhitespace()->notEmpty()->alnum(),
            'password' => v::notEmpty(),
            'email' => v::email()
        ];
        return self::validate($request, $rules);
    }
    // Return the errors in an array
    public static function getErrors()
    {
        return self::$errors;
    }
}