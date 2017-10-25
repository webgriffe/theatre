<?php

namespace AppBundle\Service;

use AppBundle\Command\BuildAllCommand;
use AppBundle\Entity\Repository;
use AppBundle\Model\Package;
use AppBundle\Model\RepositoryRepository;

class SatisJsonBuilder
{
    const HOMEPAGE_PATTERN = '%s/%s/%s/';

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function build(Repository $repository)
    {
        $require = [];
        /** @var Package $package */
        foreach ($repository->getPackages() as $package) {
            $require[$package->getName()] = $package->getVersion();
        }
        return json_encode(
            [
                'name' => $repository->getName(),
                'homepage' => sprintf(
                    self::HOMEPAGE_PATTERN,
                    rtrim($this->baseUrl, DIRECTORY_SEPARATOR),
                    BuildAllCommand::BUILDS_DIR,
                    $repository->getId()
                ),
                'repositories' => array_map(
                    function (RepositoryRepository $vcsRepository) {
                        return ['type' => 'vcs', 'url' => $vcsRepository->getUrl()];
                    },
                    $repository->getRepositories()
                ),
                'require-all' => false,
                'require' => $require,
                'archive' => [
                    'directory' => 'dist',
                    'format' => 'tar',
                    'skip-dev' => true,
                ]
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
