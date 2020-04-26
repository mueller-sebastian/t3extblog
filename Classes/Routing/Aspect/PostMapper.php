<?php
declare(strict_types = 1);

namespace FelixNagel\T3extblog\Routing\Aspect;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;
use TYPO3\CMS\Core\Site\SiteLanguageAwareTrait;

/**
 * PostMapper
 */
class PostMapper extends PersistedAliasMapper
{
    use SiteLanguageAwareTrait;

    /**
     * @var string
     */
    protected $datePrefix;

    /**
     * @var string
     */
    protected $dateFieldName;

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings)
    {
        // Set defaults
        $settings['tableName'] = $settings['tableName'] ?? 'tx_t3blog_post';
        $settings['routeFieldName'] = $settings['routeFieldName'] ?? 'url_segment';

        $dateFieldName = $settings['dateFieldName'] ?? 'date';
        $datePrefix = $settings['datePrefix'] ?? null;

        if (!is_string($dateFieldName)) {
            throw new \InvalidArgumentException('dateFieldName must be string', 1537277135);
        }

        if ($datePrefix !== null) {
            if (!is_string($datePrefix)) {
                throw new \InvalidArgumentException('datePrefix must be string', 1537277134);
            }
            $date = new \DateTime();
            if (empty($date->format($datePrefix))) {
                throw new \InvalidArgumentException('datePrefix must be valid DateTime value', 1550748751);
            }
        }

        $this->dateFieldName = $dateFieldName;
        $this->datePrefix = $datePrefix;

        parent::__construct($settings);
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $value): ?string
    {
        if ($this->datePrefix === null) {
            return parent::generate($value);
        }

        $result = $this->getPersistenceDelegate()->generate([
            'uid' => $value
        ]);
        $result = $this->resolveOverlay($result);

        if (!isset($result[$this->routeFieldName])) {
            return null;
        }

        $value = (string)$result[$this->routeFieldName];
        $value = $this->getRouteValueFromResult($result, $value);

        return $this->purgeRouteValuePrefix($value);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $value): ?string
    {
        if ($this->datePrefix === null) {
            return parent::resolve($value);
        }

        $valueBackup = $value;

        // Remove date prefix if existing
        $date = new \DateTime();
        $value = substr($value, strlen($date->format($this->datePrefix)));

        if (!is_string($value)) {
            throw new \InvalidArgumentException(
                'Value to resolve is invalid!',
                1587931928
            );
        }

        // Remove possible appended route string (e.g. "/comment")
        // Needed since TYPO3 9.5.15 and https://forge.typo3.org/issues/88291
        if (version_compare(TYPO3_version, '9.5.15', '>=')) {
            $valueSplit = preg_split('#/#', $value);
            if ($valueSplit > 0) {
                $value =  $valueSplit[0];
            }
        }

        $value = $this->routeValuePrefix . $this->purgeRouteValuePrefix($value);
        $result = $this->getPersistenceDelegate()->resolve([
            $this->routeFieldName => $value
        ]);

        $value = $this->getRouteValueFromResult($result, $value);
        if ($valueBackup !== $value) {
            return null;
        }

        if ($result[$this->languageParentFieldName] ?? null > 0) {
            return (string)$result[$this->languageParentFieldName];
        }

        if (isset($result['uid'])) {
            return (string)$result['uid'];
        }

        return null;
    }

    /**
     * @param array $result
     * @param string $value
     * @return string
     */
    protected function getRouteValueFromResult(array $result, $value)
    {
        $date = new \DateTime(date('c', (int)$result[$this->dateFieldName]));

        if ($date instanceof \DateTime) {
            $value = $date->format($this->datePrefix).$value;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    protected function buildPersistenceFieldNames(): array
    {
        $fields = parent::buildPersistenceFieldNames();
        $fields[] = $this->dateFieldName;

        return $fields;
    }
}
