<?php

namespace AppBundle\Controller\AdminV2\Marketing;

use AppBundle\Common\ArrayToolkit;
use AppBundle\Common\Paginator;
use AppBundle\Controller\AdminV2\BaseController;
use Biz\Classroom\Service\ClassroomService;
use Biz\Common\CommonException;
use Biz\Course\Service\CourseSetService;
use Biz\InformationCollect\InformationCollectException;
use Biz\InformationCollect\Service\EventService;
use Biz\InformationCollect\Service\ResultService;
use Biz\Taxonomy\Service\CategoryService;
use Symfony\Component\HttpFoundation\Request;

class InformationCollectController extends BaseController
{
    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();

        $page = new Paginator(
            $request,
            $this->getEventService()->count($conditions),
            20
        );

        $events = $this->getEventService()->search(
            $conditions,
            ['createdTime' => 'DESC'],
            $page->getOffsetCount(),
            $page->getPerPageCount()
        );

        return $this->render('admin-v2/marketing/information-collect/list.html.twig', [
            'events' => $events,
            'collectedNum' => $this->getResultService()->countGroupByEventId(ArrayToolkit::column($events, 'id')),
            'paginator' => $page,
        ]);
    }

    public function closeAction(Request $request, $id)
    {
        $this->getEventService()->closeCollection($id);

        return $this->createJsonResponse(true);
    }

    public function openAction(Request $request, $id)
    {
        $this->getEventService()->openCollection($id);

        return $this->createJsonResponse(true);
    }

    public function createAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->getEventService()->createEventWithLocations($request->request->all());

            return $this->createJsonResponse(true);
        }

        $allCourseLocation = $this->getEventService()->searchLocations(['targetType' => 'course', 'targetId_LTE' => 0], [], 0, 1);
        $allClassroomLocation = $this->getEventService()->searchLocations(['targetType' => 'classroom', 'targetId_LTE' => 0], [], 0, 1);

        return $this->render('admin-v2/marketing/information-collect/edit/index.html.twig', [
            'allCourseLocation' => empty($allCourseLocation) ? [] : $allCourseLocation[0],
            'allClassroomLocation' => empty($allClassroomLocation) ? [] : $allClassroomLocation[0],
        ]);
    }

    public function updateAction(Request $request, $id)
    {
        $event = $this->getEventService()->get($id);

        if (empty($event)) {
            $this->createNewException(InformationCollectException::NOTFOUND_COLLECTION());
        }

        if ($request->isMethod('POST')) {
            $event = $this->getEventService()->updateEventWithLocations($event['id'], $request->request->all());

            return $this->createJsonResponse(true);
        }

        $conditions = ['eventId' => $event['id']];
        $locations = $this->getEventService()->searchLocations(
            $conditions,
            [],
            0,
            $this->getEventService()->countLocations($conditions),
            ['targetType', 'targetId']
        );

        $locationInfo = [];
        foreach ($locations as $location) {
            if ('course' == $location['targetType']) {
                $locationInfo['courseIds'][] = $location['targetId'];
            } else {
                $locationInfo['classroomIds'][] = $location['targetId'];
            }
        }

        $allCourseLocation = $this->getEventService()->searchLocations(['targetType' => 'course', 'targetId_LTE' => '0'], [], 0, 1);
        $allClassroomLocation = $this->getEventService()->searchLocations(['targetType' => 'classroom', 'targetId_LTE' => '0'], [], 0, 1);

        return $this->render('admin-v2/marketing/information-collect/edit/index.html.twig', [
            'event' => $event,
            'locationInfo' => $locationInfo,
            'allCourseLocation' => empty($allCourseLocation) ? [] : $allCourseLocation[0],
            'allClassroomLocation' => empty($allClassroomLocation) ? [] : $allClassroomLocation[0],
        ]);
    }

    public function targetModalAction(Request $request, $type)
    {
        return $this->render('admin-v2/marketing/information-collect/edit/select/select-target-modal.html.twig', [
            'type' => $type,
            'eventId' => $request->query->get('eventId'),
        ]);
    }

    public function chooserAction(Request $request, $type)
    {
        list($users, $targets, $categories, $paginator) = $this->searchTargetsByTypeAndConditions($type, $request->query->all());

        return $this->render('admin-v2/marketing/information-collect/edit/chooser/index.html.twig', [
            'type' => $type,
            'users' => $users,
            'targets' => $targets,
            'categories' => $categories,
            'paginator' => $paginator,
            'eventId' => $request->query->get('eventId'),
        ]);
    }

    public function ajaxChooserAction(Request $request, $type)
    {
        list($users, $targets, $categories, $paginator) = $this->searchTargetsByTypeAndConditions($type, $request->query->all(), true);

        return $this->render(
            "admin-v2/marketing/information-collect/edit/chooser/{$type}-table.html.twig",
            [
                'type' => $type,
                'users' => $users,
                'targets' => $targets,
                'categories' => $categories,
                'paginator' => $paginator,
            ]
        );
    }

    public function selectedChooserAction(Request $request, $type)
    {
        if ($request->isMethod('POST')) {
            $selectedIds = $request->request->get('ids', []);
        } else {
            $selectedIds = $request->query->get('ids', []);
        }

        $conditions = ['ids' => empty($selectedIds) ? [-1] : $selectedIds];

        if (EventService::TARGET_TYPE_COURSE == $type) {
            list($targets, $paginator) = $this->searchCourseSets($conditions);
        } else {
            list($targets, $paginator) = $this->searchClassrooms($conditions);
        }

        $paginator->setBaseUrl($this->generateUrl('admin_v2_information_collect_chooser_selected', ['type' => $type]));

        $locationConditions = ['targetIds' => $conditions['ids'], 'targetType' => $type, 'excludeEventId' => $request->query->get('excludeEventId', 0)];
        $locations = $this->getEventService()->searchLocations($locationConditions, [], 0, count($selectedIds), ['targetId']);
        $locations = ArrayToolkit::index($locations, 'targetId');

        return $this->render(
            'admin-v2/marketing/information-collect/edit/select/selected-target-table.html.twig',
            [
                'type' => $type,
                'targets' => ArrayToolkit::index($targets, 'id'),
                'selectedTargetIds' => $selectedIds,
                'locations' => $locations,
                'paginator' => $paginator,
                'hasRelated' => $this->getEventService()->countLocations($locationConditions) > 0,
            ]
        );
    }

    public function detailAction(Request $request, $id)
    {
        $event = $this->getEventService()->get($id);
        if (empty($event)) {
            $this->createNewException(InformationCollectException::NOTFOUND_COLLECTION());
        }

        $conditions = $request->query->all();
        $conditions['eventId'] = $id;
        $paginator = new Paginator(
            $request,
            $this->getResultService()->count($conditions),
            20);

        $collectedData = $this->getResultService()->searchCollectedData(
            $conditions,
            ['createdTime' => 'DESC'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($collectedData, 'userId'));
        $userProfiles = $this->getUserService()->findUserProfilesByIds(ArrayToolkit::column($users, 'id'));

        return $this->render('admin-v2/marketing/information-collect/detail.html.twig', [
            'event' => $event,
            'collectedNum' => $this->getResultService()->countGroupByEventId($id),
            'creator' => $this->getUserService()->getUser($event['creator']),
            'collectedData' => $collectedData,
            'users' => ArrayToolkit::index($users, 'id'),
            'profiles' => ArrayToolkit::index($userProfiles, 'id'),
            'labels' => $this->getEventService()->findItemsByEventId($id),
            'resultData' => $this->getResultService()->findResultDataByResultIds(ArrayToolkit::column($collectedData, 'id')),
            'paginator' => $paginator,
        ]);
    }

    private function searchTargetsByTypeAndConditions($type, array $conditions, $isAjax = false)
    {
        if (!in_array($type, [EventService::TARGET_TYPE_COURSE, EventService::TARGET_TYPE_CLASSROOM])) {
            $this->createNewException(CommonException::ERROR_PARAMETER());
        }

        if (EventService::TARGET_TYPE_COURSE == $type) {
            list($targets, $paginator) = $this->searchCourseSets($conditions, $isAjax);
            $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($targets, 'creator'));
        } else {
            list($targets, $paginator) = $this->searchClassrooms($conditions, $isAjax);
            $users = [];
        }

        if (!$isAjax) {
            $paginator->setBaseUrl($this->generateUrl('admin_v2_information_collect_chooser_ajax', ['type' => $type]));
        }

        $categories = $this->getCategoryService()->findCategoriesByIds(ArrayToolkit::column($targets, 'categoryId'));

        return [$users, $targets, $categories, $paginator];
    }

    private function searchCourseSets($conditions)
    {
        $conditions['parentId'] = 0;

        $count = $this->getCourseSetService()->countCourseSets($conditions);
        $paginator = new Paginator($this->get('request'), $count, 10);

        $courseSets = $this->getCourseSetService()->searchCourseSets(
            $conditions,
            ['createdTime' => 'DESC'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return [$courseSets, $paginator];
    }

    private function searchClassrooms($conditions)
    {
        $conditions['parentId'] = 0;
        if (isset($conditions['ids'])) {
            $conditions['classroomIds'] = $conditions['ids'];
            unset($conditions['ids']);
        }

        $count = $this->getClassroomService()->countClassrooms($conditions);
        $paginator = new Paginator($this->get('request'), $count, 10);

        $classrooms = $this->getClassroomService()->searchClassrooms(
            $conditions,
            ['createdTime' => 'DESC'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return [$classrooms, $paginator];
    }

    /**
     * @return EventService
     */
    public function getEventService()
    {
        return $this->createService('InformationCollect:EventService');
    }

    /**
     * @return ResultService
     */
    public function getResultService()
    {
        return $this->createService('InformationCollect:ResultService');
    }

    /**
     * @return CourseSetService
     */
    private function getCourseSetService()
    {
        return $this->createService('Course:CourseSetService');
    }

    /**
     * @return CategoryService
     */
    protected function getCategoryService()
    {
        return $this->createService('Taxonomy:CategoryService');
    }

    /**
     * @return ClassroomService
     */
    protected function getClassroomService()
    {
        return $this->createService('Classroom:ClassroomService');
    }
}
