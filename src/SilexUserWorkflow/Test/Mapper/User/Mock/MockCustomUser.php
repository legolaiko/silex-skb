<?php


namespace SilexUserWorkflow\Test\Mapper\User\Mock;


use SilexUserWorkflow\Mapper\User\Entity\User;

class MockCustomUser extends User
{
    protected $myField;

    /**
     * @return mixed
     */
    public function getMyField()
    {
        return $this->myField;
    }

    /**
     * @param mixed $myField
     */
    public function setMyField($myField)
    {
        $this->myField = $myField;
    }


} 