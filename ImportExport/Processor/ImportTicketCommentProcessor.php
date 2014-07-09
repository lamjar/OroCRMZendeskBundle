<?php

namespace OroCRM\Bundle\ZendeskBundle\ImportExport\Processor;

use Oro\Bundle\ImportExportBundle\Exception\InvalidArgumentException;

use OroCRM\Bundle\ZendeskBundle\Entity\TicketComment;
use OroCRM\Bundle\ZendeskBundle\Model\SyncHelper\TicketCommentSyncHelper;

class ImportTicketCommentProcessor extends AbstractImportProcessor
{
    /**
     * @var TicketCommentSyncHelper
     */
    protected $helper;

    /**
     * @param TicketCommentSyncHelper $helper
     */
    public function __construct(TicketCommentSyncHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function process($entity)
    {
        if (!$entity instanceof TicketComment) {
            throw new InvalidArgumentException(
                sprintf(
                    'Imported entity must be instance of OroCRM\Bundle\ZendeskBundle\Entity\TicketComment, %s given.',
                    is_object($entity) ? get_class($entity) : gettype($entity)
                )
            );
        }

        if (!$this->validateOriginId($entity)) {
            return null;
        }

        $this->getLogger()->setMessagePrefix("Zendesk Ticket Comment [origin_id={$entity->getOriginId()}]: ");

        if (!$entity->getTicket()) {
            $this->getLogger()->error("Comment Ticket required.");
            $this->getContext()->incrementErrorEntriesCount();
            return null;
        }
        $ticketId = $entity->getTicket()->getOriginId();

        $this->helper->setLogger($this->getLogger());
        $this->helper->refreshEntity($entity, $this->getChannel());

        if (!$entity->getTicket()) {
            $this->getLogger()->error("Ticket not found [origin_id=$ticketId].");
            $this->getContext()->incrementErrorEntriesCount();
            return null;
        }

        $existingComment = $this->helper->findEntity($entity, $this->getChannel());

        if ($existingComment) {
            $this->helper->copyEntityProperties($existingComment, $entity);
            $entity = $existingComment;

            $this->getLogger()->info("Update found Zendesk ticket comment.");
            $this->getContext()->incrementUpdateCount();
        } else {
            $this->getLogger()->info("Add new Zendesk ticket comment.");
            $this->getContext()->incrementAddCount();
        }

        $this->helper->syncRelatedEntities($entity, $this->getChannel());

        return $entity;
    }
}