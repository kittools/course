<?php
namespace Kittools\Course\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * 课程安排service
 *
 * @author yanfeng
 */
class CourseService
{

    protected $doctrine;

    protected $em;

    protected static $class;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
    }

    /**
     * 排课安排
     *
     * @param string $class
     * @param array $params
     * @return array
     */
    public function course(string $class, array $params = []): array
    {
        self::$class = $class;
        $isOccupy = self::isOccupy($params);

        // 无占用情况 可以排课
        if (empty($isOccupy)) {
            $course = new $class();
            foreach ($params as $key => $val) {
                $method = 'set' . ucwords($key, '_');
                if (method_exists($course, $method)) {
                    $course->$method($val);
                }
            }
            $this->em->persist($course);
            $this->em->flush();
            return [
                'code' => 1,
                'msg' => '排课成功'
            ];
        } else {
            return [
                'code' => 0,
                'msg' => $isOccupy
            ];
        }
    }

    /**
     * 验证占用情况
     *
     * @param array $params
     * @return boolean
     */
    protected static function isOccupy(array $params = [])
    {
        $reposeMsg = [];
        $time = $params['time'] ?? date('YmdHis');
        $reposeMsg[] = self::isOccupyClass($params['class'], $time);
        $reposeMsg[] = self::isOccupyRoom($params['room'], $time);
        $reposeMsg[] = self::isOccupyTeacher($params['teacher'], $time);

        return $reposeMsg;
    }

    /**
     * 判断当前时间教室是否占用
     *
     * @param array $room
     * @param string $time
     */
    protected static function isOccupyRoom(int $room, string $time)
    {
        $repo = self::getRepo();
        $occupy = $repo->findBy([
            'roomId' => $room,
            'courseTime' => $time
        ]);
        return empty($occupy) ? '当前教室已占用' : false;
    }

    /**
     * 判断当前时间教师是否有课
     *
     * @param int $teacher
     * @param string $time
     * @return string|boolean
     */
    protected static function isOccupyTeacher(int $teacher, string $time)
    {
        $repo = self::getRepo();
        $occupy = $repo->findBy([
            'teacherId' => $teacher,
            'courseTime' => $time
        ]);
        return empty($occupy) ? '当前教师已有其他授课课程' : false;
    }

    /**
     * 判断当前时间班级是否有课
     *
     * @param int $class
     * @param string $time
     * @return string|boolean
     */
    protected static function isOccupyClass(int $class, string $time)
    {
        $repo = self::getRepo();
        $occupy = $repo->findBy([
            'classId' => $class,
            'courseTime' => $time
        ]);
        return empty($occupy) ? '当前班级已有其他课程安排' : false;
    }

    /**
     * 获取Repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepo()
    {
        return $this->doctrine->getRepository(static::$class);
    }
}