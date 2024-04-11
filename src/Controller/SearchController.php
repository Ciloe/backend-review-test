<?php

namespace App\Controller;

use App\Dto\SearchInput;
use App\Repository\ReadEventRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SearchController
{
    private ReadEventRepository $repository;
    private SerializerInterface $serializer;

    public function __construct(
        ReadEventRepository $repository,
        SerializerInterface $serializer
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    #[Route('/api/search', name: "api_search", methods: ["GET"])]
    public function searchCommits(Request $request): JsonResponse
    {
        /** @var SearchInput $searchInput */
        $searchInput = $this->serializer->denormalize($request->query->all(), SearchInput::class);
        if (!$searchInput->isInitialized()) {
            throw new NotFoundHttpException('Requested parameters not sent');
        }

        $countByType = $this->repository->countByType($searchInput);

        $data = [
            'meta' => [
                'totalEvents' => $this->repository->countAll($searchInput),
                'totalPullRequests' => $countByType['pullRequest'] ?? 0,
                'totalCommits' => $countByType['commit'] ?? 0,
                'totalComments' => $countByType['comment'] ?? 0,
            ],
            'data' => [
                'events' => $this->repository->getLatest($searchInput),
                'stats' => $this->repository->statsByTypePerHour($searchInput)
            ]
        ];

        return new JsonResponse($data);
    }
}
