<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;

/**
 * SubscriberController.
 */
class PostSubscriberController extends AbstractSubscriberController
{
    /**
     * subscriberRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $subscriberRepository;

    /**
     * blogSubscriberRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $blogSubscriberRepository;

    /**
     * subscriber.
     *
     * @var \FelixNagel\T3extblog\Domain\Model\PostSubscriber
     */
    protected $subscriber = null;

    /**
     * {@inheritdoc}
     */
    protected function initializeAction()
    {
        parent::initializeAction();

        $this->subscriptionSettings = $this->settings['subscriptionManager']['comment']['subscriber'];
    }

    /**
     * action list.
     */
    public function listAction()
    {
        $this->checkAuth();

        $this->redirect('list', 'Subscriber');
    }

    /**
     * action delete.
     *
     * @param \FelixNagel\T3extblog\Domain\Model\PostSubscriber $subscriber
     *
     * @throws InvalidArgumentValueException
     */
    public function deleteAction($subscriber = null)
    {
        parent::deleteAction($subscriber);
    }

    /**
     * Finds existing subscriptions.
     *
     * @param PostSubscriber $subscriber
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    protected function findExistingSubscriptions($subscriber)
    {
        return $this->subscriberRepository->findExistingSubscriptions(
            $subscriber->getPostUid(),
            $subscriber->getEmail(),
            $subscriber->getUid()
        );
    }
}
