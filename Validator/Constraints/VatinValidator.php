<?php

namespace Ddeboer\VatinBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Ddeboer\Vatin\Validator;
use Ddeboer\Vatin\Exception\ViesException;

/**
 * Validate a VAT identification number using the ddeboer/vatin library
 *
 */
class VatinValidator extends ConstraintValidator
{
    /**
     * VATIN validator
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Constructor
     *
     * @param Validator $validator VATIN validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if ($this->isValidVatin($value, $constraint->checkExistence, $constraint->validIfError)) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }

    /**
     * Is the value a valid VAT identification number?
     *
     * @param string $value          Value
     * @param bool   $checkExistence Also check whether the VAT number exists
     * @param bool   $validIfError   Return valid/invalid if server is down.
     *                               Default/null throws ViesException
     *
     * @return bool
     */
    protected function isValidVatin($value, $checkExistence, $validIfError = null)
    {
        try {
            return $this->validator->isValid($value, $checkExistence);
        } catch (ViesException $e) {
            if ($validIfError) {
                return true;
            } else {
                throw $e;
            }
        }
    }
}
