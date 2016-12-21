<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\App\Core\Model;

use Cradle\App\Core\AbstractModel;
use Cradle\App\Core\Validator;

use Elasticsearch\Common\Exceptions\NoNodesAvailableException;

/**
 * Profile Model
 *
 * @vendor   Custom
 * @package  Core
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Profile extends AbstractModel
{
    /**
     * @const CACHE_SEARCH Cache search key
     */
    const CACHE_SEARCH = 'core-profile-search';

    /**
     * @const CACHE_DETAIL Cache detail key
     */
    const CACHE_DETAIL = 'core-profile-detail';

    /**
     * @const INDEX_NAME Index name
     */
    const INDEX_NAME = 'profile';

    /**
     * Create in database
     *
     * @param array $data
     *
     * @return array
     */
    public function databaseCreate(array $data)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model($data)
            ->setProfileCreated(date('Y-m-d H:i:s'))
            ->setProfileUpdated(date('Y-m-d H:i:s'))
            ->save('profile')
            ->get();
    }

    /**
     * Get detail from database
     *
     * @param *int $id
     *
     * @return array
     */
    public function databaseDetail($id)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        $search = $service->search('profile');

        if(!is_numeric($id) && $id) {
            $search->filterByProfileSlug($id);
        } else {
            $search->filterByProfileId($id);
        }

        $results = $search->getRow();

        if(!$results) {
            return $results;
        }

        //comments
        $results['comment'] = $service
            ->search('comment')
            ->setColumns(
                'comment.*',
                'profile.*',
                'profile_comment.profile_id AS about_id'
            )
            ->innerJoinUsing('profile_comment', 'comment_id')
            ->innerJoinUsing('comment_profile', 'comment_id')
            ->innerJoinOn(
                'profile',
                'comment_profile.profile_id = profile.profile_id'
            )
            ->addFilter(
                'profile_comment.profile_id = %s',
                $results['profile_id']
            )
            ->filterByCommentActive(1)
            ->filterByProfileActive(1)
            ->getRows();

        //achievements
        if($results['profile_achievements']) {
            $results['profile_achievements'] = json_decode($results['profile_achievements'], true);
        }  else {
            $results['profile_achievements'] = [];
        }

        //product count
        $results['product'] = $service
            ->search('product')
            ->innerJoinUsing('product_profile', 'product_id')
            ->filterByProductActive(1)
            ->filterByProfileId($results['profile_id'])
            ->getTotal();

        return $results;
    }

    /**
     * Remove from database
     * PLEASE BECAREFUL USING THIS !!!
     * It's here for clean up scripts
     *
     * @param *int $id
     */
    public function databaseRemove($id)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        //please rely on SQL CASCADING ON DELETE
        return $service
            ->model()
            ->setProfileId($id)
            ->remove('profile');
    }

    /**
     * Search in database
     *
     * @param array $data
     *
     * @return array
     */
    public function databaseSearch(array $data = [])
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        $filter = [];
        $range = 50;
        $start = 0;
        $order = [];
        $count = 0;
        $keywords = null;

        if (isset($data['filter']) && is_array($data['filter'])) {
            $filter = $data['filter'];
        }

        if (isset($data['range']) && is_numeric($data['range'])) {
            $range = $data['range'];
        }

        if (isset($data['start']) && is_numeric($data['start'])) {
            $start = $data['start'];
        }

        if (isset($data['order']) && is_array($data['order'])) {
            $order = $data['order'];
        }

        if (isset($data['q']) && is_array($data['q'])) {
            $keywords = $data['q'];
        }

        if (!isset($filter['profile_active'])) {
            $filter['profile_active'] = 1;
        }

        $search = $service
            ->search('profile')
            ->setStart($start)
            ->setRange($range);

        //add filters
        foreach ($filter as $column => $value) {
            if (preg_match('/^[a-zA-Z0-9-_]+$/', $column)) {
                $search->addFilter($column . ' = %s', $value);
            }
        }

        //keyword?
        if (isset($keywords)) {
            foreach ($keywords as $keyword) {
                $or = [];
                $where = [];

                $where[] = 'LOWER(profile_name) LIKE %s';
                $or[] = '%' . strtolower($keyword) . '%';

                array_unshift($or, '(' . implode(' OR ', $where) . ')');

                call_user_func([$search, 'addFilter'], ...$or);
            }
        }

        //add sorting
        foreach ($order as $sort => $direction) {
            $search->addSort($sort, $direction);
        }

        //return response format
        return [
            'rows' => $search->getRows(),
            'total' => $search->getTotal()
        ];
    }

    /**
     * Update to database
     *
     * @param array $data
     *
     * @return array
     */
    public function databaseUpdate(array $data)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model($data)
            ->setProfileUpdated(date('Y-m-d H:i:s'))
            ->save('profile')
            ->get();
    }

    /**
     * Checks to see if email or phone already exists
     *
     * @param *string $email
     * @param *string $phone
     * @param *string $type
     *
     * @return bool
     */
    public function exists($email = null, $phone = null, $type = null)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        $search = $service->search('profile');

        if($type) {
            $search->filterByProfileType($type);
        }

        if ($email && $phone) {
            $search->addFilter(
                '(profile_email = %s OR profile_phone = %s)',
                $email,
                $phone
            );
        } else if ($email) {
            $search->filterByProfileEmail($email);
        //profile phone
        } else {
            $search->filterByProfilePhone($phone);
        }

        return $search->getRow();
    }

    /**
     * Search in index
     *
     * @param array $data
     *
     * @return array
     */
    public function indexSearch(array $data = [])
    {
        $service = $this->service->index();

        if(!$service) {
            return false;
        }

        //set the defaults
        $filter = [];
        $range = 50;
        $start = 0;
        $order = ['profile_id' => 'asc'];
        $count = 0;

        //merge passed data with default data
        if (isset($data['filter']) && is_array($data['filter'])) {
            $filter = $data['filter'];
        }

        if (isset($data['range']) && is_numeric($data['range'])) {
            $range = $data['range'];
        }

        if (isset($data['start']) && is_numeric($data['start'])) {
            $start = $data['start'];
        }

        if (isset($data['order']) && is_array($data['order'])) {
            $order = $data['order'];
        }

        //prepare the search object
        $search = [];

        //keyword search
        if (isset($data['q'])) {
            if(!is_array($data['q'])) {
                $data['q'] = [$data['q']];
            }

            foreach($data['q'] as $keyword) {
                $search['query']['bool']['filter'][]['query_string'] = [
                    'query' => $keyword . '*',
                    'fields' => ['profile_name', 'profile_email', 'profile_locale'],
                    'default_operator' => 'AND'
                ];
            }
        }

        //generic full match filters

        //profile_active
        if (!isset($filter['profile_active'])) {
            $filter['profile_active'] = 1;
        }

        foreach($filter as $key => $value) {
            $search['query']['bool']['filter'][]['term'][$key] = $value;
        }

        //add sorting
        foreach ($order as $sort => $direction) {
            $search['sort'] = [$sort => $direction];
        }

        try {
            $results = $service->search([
                'index' => static::INDEX_NAME,
                'type' => static::INDEX_TYPE,
                'body' => $search,
                'size' => $range,
                'from' => $start
            ]);
        } catch(NoNodesAvailableException $e) {
            return false;
        }

        // fix it
        $rows = array();

        foreach ($results['hits']['hits'] as $item) {
            $rows[] = $item['_source'];
        }

        //return response format
        return [
            'rows' => $rows,
            'total' => $results['hits']['total']
        ];
    }

    /**
     * Returns Create Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getCreateErrors(array $data, array $errors = [])
    {
        // profile_name - required
        if (!isset($data['profile_name']) || empty($data['profile_name'])) {
            $errors['profile_name'] = 'Cannot be empty';
        }

        // profile_locale - required
        if (!isset($data['profile_locale']) || empty($data['profile_locale'])) {
            $errors['profile_locale'] = 'Cannot be empty';
        }

        //also add optional errors
        return self::getOptionalErrors($data, $errors);
    }

    /**
     * Returns Product Update Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getUpdateErrors(array $data, array $errors = [])
    {
        // profile_id            Required
        if (!isset($data['profile_id']) || empty($data['profile_id'])) {
            $errors['profile_id'] = 'Cannot be empty';
        }

        //profile_name        Required
        if (isset($data['profile_name']) && empty($data['profile_name'])) {
            $errors['profile_name'] = 'Cannot be empty, if set';
        }

        //profile_locale        Required
        if (isset($data['profile_locale']) && empty($data['profile_locale'])) {
            $errors['profile_locale'] = 'Cannot be empty, if set';
        }

        //also add optional errors
        return self::getOptionalErrors($data, $errors);
    }

    /**
     * Returns Optional Errors
     *
     * @param *array $data
     * @param array  $errors
     *
     * @return array
     */
    public function getOptionalErrors(array $data, array $errors = [])
    {
        // profile_gender - one of
        $choices = ['male', 'female'];
        if (isset($data['profile_gender']) && !in_array($data['profile_gender'], $choices)) {
            $errors['profile_gender'] = sprintf('Should be one of %s', implode(',', $choices));
        }

        // profile_birth - date
        if (isset($data['profile_birth']) && !Validator::isUrl($data['profile_birth'])) {
            $errors['profile_birth'] = 'Must be a valid date YYYY-MM-DD';
        }

        // profile_facebook - url
        if (isset($data['profile_facebook']) && !Validator::isUrl($data['profile_facebook'])) {
            $errors['profile_facebook'] = 'Should be a valid URL';
        }

        // profile_linkedin - url
        if (isset($data['profile_linkedin']) && !Validator::isUrl($data['profile_linkedin'])) {
            $errors['profile_linkedin'] = 'Should be a valid URL';
        }

        // profile_twitter - url
        if (isset($data['profile_twitter']) && !Validator::isUrl($data['profile_twitter'])) {
            $errors['profile_twitter'] = 'Should be a valid URL';
        }

        // profile_google - url
        if (isset($data['profile_google']) && !Validator::isUrl($data['profile_google'])) {
            $errors['profile_google'] = 'Should be a valid URL';
        }

        // profile_rating - small
        if (isset($data['profile_rating']) && !Validator::isSmall($data['profile_rating'])) {
            $errors['profile_rating'] = 'Should be between 0 and 9';
        }

        // profile_experience - int
        if (isset($data['profile_experience']) && !Validator::isInt($data['profile_experience'])) {
            $errors['profile_experience'] = 'Must be a valid integrer';
        }

        if (isset($data['profile_email']) && !Validator::isEmail($data['profile_email'])) {
            $errors['profile_email'] = 'Must be a valid e-mail address';
        //mailinator
        } else if (isset($data['profile_email']) &&
            strpos(strtolower($data['profile_email']), 'mailinator') !== false) {
            $errors['profile_email'] = 'This email has been blocked';
        }

        if (isset($data['profile_phone']) && preg_match('/[a-zA-Z]/i', $data['profile_phone'])) {
            $errors['profile_phone'] = 'Must be a valid phone number';
        }

        // profile_flag - small
        if (isset($data['profile_flag']) && !Validator::isSmall($data['profile_flag'])) {
            $errors['profile_flag'] = 'Should be between 0 and 9';
        }

        return $errors;
    }

    /**
     * Link to comment
     *
     * @param *int $profileId
     * @param *int $commentId
     */
    public function linkComment($profileId, $commentId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setProfileId($profileId)
            ->setCommentId($commentId)
            ->insert('profile_comment');
    }

    /**
     * Unlinks all comment
     *
     * @param *int $productId
     */
    public function unlinkAllComment()
    {
    }

    /**
     * Unlinks comment
     *
     * @param *int $profileId
     * @param *int $commentId
     */
    public function unlinkComment($profileId, $commentId)
    {
        $service = $this->service->database();

        if(!$service) {
            return false;
        }

        return $service
            ->model()
            ->setProfileId($profileId)
            ->setCommentId($commentId)
            ->remove('profile_comment');
    }
}
