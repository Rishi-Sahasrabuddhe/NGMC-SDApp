<?php

declare(strict_types=1);

namespace megarabyte\writtenbooks;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\{
    ItemStack,
    ItemStackWrapper,
    UseItemTransactionData
};
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\math\Vector3;

class OpenBook
{
    private Player $player;
    private Book $book;


    function __construct(Player $player, Book $book)
    {
        $this->player = $player;
        $this->book = $book;
        $originalItem = $this->player->getInventory()->getItemInHand();
        $this->process($this->book->convertToItem());
        $this->player->getInventory()->setItemInHand($originalItem);
    }

    private function process(Item $item)
    {
        foreach ($this->book->pages as $id => $page) $item->setPageText($id, $page->getText());

        // DO NOT EDIT AFTER THIS POINT
        $networkSession = $this->player->getNetworkSession();
        $networkSession->getInvManager()->syncSlot(
            $this->player->getInventory(),
            $this->player->getInventory()->getHeldItemIndex(),
            $networkSession->getTypeConverter()->coreItemStackToNet($item)
        );
        $networkSession->sendDataPacket(InventoryTransactionPacket::create(
            0,
            [],
            UseItemTransactionData::new(
                [],
                UseItemTransactionData::ACTION_CLICK_AIR,
                new BlockPosition(0, 0, 0),
                0,
                0,
                ItemStackWrapper::legacy(ItemStack::null()),
                Vector3::zero(),
                Vector3::zero(),
                0
            )
        ));
    }
}
