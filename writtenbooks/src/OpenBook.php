<?php

declare(strict_types=1);

namespace megarabyte\writtenbooks;

use pocketmine\player\Player;

use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\{
    ItemStack,
    ItemStackWrapper,
    UseItemTransactionData
};

class OpenBook
{
    function __construct(Player $player, Book $book)
    {
        $originalItem = $player->getInventory()->getItemInHand();
        $networkSession = $player->getNetworkSession();

        $player->getInventory()->setItemInHand($book);

        $networkSession->getInvManager()->syncSlot(
            $player->getInventory(),
            $player->getInventory()->getHeldItemIndex(),
            $networkSession->getTypeConverter()->coreItemStackToNet($book)
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
        $player->getInventory()->setItemInHand($originalItem);
    }
}
