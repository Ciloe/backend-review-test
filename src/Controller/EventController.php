<?php

namespace App\Controller;

use App\Dto\EventCommentInput;
use App\Repository\ReadEventRepository;
use App\Repository\WriteEventRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventController
{
    private WriteEventRepository $writeEventRepository;
    private ReadEventRepository $readEventRepository;
    private SerializerInterface $serializer;

    public function __construct(
        WriteEventRepository $writeEventRepository,
        ReadEventRepository $readEventRepository,
        SerializerInterface $serializer
    ) {
        $this->writeEventRepository = $writeEventRepository;
        $this->readEventRepository = $readEventRepository;
        $this->serializer = $serializer;
    }

    #[Route('/api/event/{id}/update-comment', name: "api_commit_update", methods: ["PUT"])]
    public function update(Request $request, int $id, ValidatorInterface $validator): Response
    {
        $eventInput = $this->serializer->deserialize($request->getContent(), EventCommentInput::class, 'json');
        if (!$eventInput->isInitialized()) {
            return new JsonResponse(
                ['message' => 'Requested parameters not sent'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $errors = $validator->validate($eventInput);

        if (\count($errors) > 0) {
            return new JsonResponse(
                ['message' => $errors->get(0)->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($this->readEventRepository->exist($id) === false) {
            return new JsonResponse(
                ['message' => sprintf('Event identified by %d not found !', $id)],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $this->writeEventRepository->updateComment($eventInput, $id);
        } catch (Exception) {
            return new Response(null, Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
