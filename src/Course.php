<?php
namespace Kittools\Course;

class Course
{

    /**
     *
     * @var array
     */
    protected $teacher;

    /**
     *
     * @var array
     */
    protected $room;

    /**
     *
     * @var array
     */
    protected $class;

    /**
     *
     * @var \DateTime
     */
    protected $time;

    public function __construct(array $teacher = array(), array $room = [], array $class = [], $time = new \DateTime())
    {
        $this->teacher = $teacher;
        $this->room = $room;
        $this->class = $class;
        $this->time = $time;
    }
}

