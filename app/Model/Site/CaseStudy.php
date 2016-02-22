<?php
class CaseStudy extends AppModel {

    public function beforeFind($queryData) {

        parent::beforeFind($queryData);
        $defaultConditions = array('CaseStudy.flag' => 0);
        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
    }
}
?>