<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * CommentAllowedViewHelper.
 */
class CommentAllowedViewHelper extends AbstractConditionViewHelper
{
    /**
     * {@inheritdoc}
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'post',
            Post::class,
            'Post object to check if new comments are allowed.',
            true
        );
    }

    /**
     * @inheritDoc
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext)
    {
        $arguments['settings'] = $renderingContext->getVariableProvider()->get('settings');

        return parent::verdict($arguments, $renderingContext);
    }

    /**
     * @inheritdoc
     */
    protected static function evaluateCondition($arguments = null)
    {
        /* @var Post $post */
        $post = $arguments['post'];
        $settings = $arguments['settings'];

        if (!$settings['blogsystem']['comments']['allowed'] || $post->getAllowComments() === 1) {
            return false;
        }

        if ($post->getAllowComments() === 2 && empty(GeneralUtility::getTsFe()->loginUser)) {
            return false;
        }

        if ($settings['blogsystem']['comments']['allowedUntil']) {
            if ($post->isExpired(trim($settings['blogsystem']['comments']['allowedUntil']))) {
                return false;
            }
        }

        return true;
    }
}
