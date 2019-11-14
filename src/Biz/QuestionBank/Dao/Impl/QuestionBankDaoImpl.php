<?php

namespace Biz\QuestionBank\Dao\Impl;

use Biz\QuestionBank\Dao\QuestionBankDao;
use Codeages\Biz\Framework\Dao\AdvancedDaoImpl;

class QuestionBankDaoImpl extends AdvancedDaoImpl implements QuestionBankDao
{
    protected $table = 'question_bank';

    public function declares()
    {
        $declares['timestamps'] = array(
            'createdTime',
            'updatedTime',
        );

        $declares['orderbys'] = array(
            'id',
        );

        $declares['conditions'] = array(
            'id = :id',
            'categoryId = :categoryId',
            'orgCode like :likeOrgCode',
        );

        return $declares;
    }
}