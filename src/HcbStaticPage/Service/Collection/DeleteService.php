<?php
namespace HcbStaticPage\Service\Collection;

use HcBackend\Data\Collection\Entities\ByIdsInterface;
use HcBackend\Service\CommandInterface;
use HcbStaticPage\Entity\StaticPage as StaticPageEntity;
use Doctrine\ORM\EntityManagerInterface;
use Zf2Libs\Stdlib\Service\Response\Messages\Response;

class DeleteService implements CommandInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var ByIdsInterface
     */
    protected $deleteData;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Response $response
     * @param ByIdsInterface $deleteData
     */
    public function __construct(EntityManagerInterface $entityManager,
                                Response $response,
                                ByIdsInterface $deleteData)
    {
        $this->entityManager = $entityManager;
        $this->response = $response;
        $this->deleteData = $deleteData;
    }

    /**
     * @return Response
     */
    public function execute()
    {
        return $this->delete($this->deleteData);
    }

    /**
     * @param \HcBackend\Data\Collection\Entities\ByIdsInterface $postsToDelete
     * @internal param \HcBackend\Data\Collection\Entities\ByIdsInterface $clientsToBlock
     * @return Response
     */
    protected  function delete(ByIdsInterface $postsToDelete)
    {
        try {
            $this->entityManager->beginTransaction();
            $staticPageEntities = $postsToDelete->getEntities();

            /* @var $staticPageEntities StaticPageEntity[] */
            foreach ($staticPageEntities as $staticPageEntity) {
                $this->entityManager->remove($staticPageEntity);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->response->error($e->getMessage())->failed();
            return $this->response;
        }

        $this->response->success();
        return $this->response;
    }
}
