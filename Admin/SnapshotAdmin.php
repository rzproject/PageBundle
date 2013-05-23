<?php

/*
 * This file is part of the RzPageBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  Rz\PageBundle\Admin;

use Sonata\PageBundle\Admin\SnapshotAdmin as BaseSnapshotAdmin;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Admin definition for the Snapshot class
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SnapshotAdmin extends BaseSnapshotAdmin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('publicationDateStart')
            ->add('publicationDateEnd')
//            ->add('content')
        ;
    }
}
