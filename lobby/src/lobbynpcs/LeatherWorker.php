<?php

declare(strict_types=1);

namespace megarabyte\lobby\lobbynpcs;

use megarabyte\npc\HumanNPC;
use megarabyte\quest\QuestData;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\entity\Human;
use pocketmine\math\Vector3;
use pocketmine\entity\Location;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\ClickSound;
use pocketmine\world\sound\ScrapeSound;
use pocketmine\world\sound\XpCollectSound;

class LeatherWorker
{

    private Player $player;
    private Human $leatherWorker;
    private InvMenu $invMenu;

    private Item $buypoints;
    private Item $buyleather;

    public function __construct(Player $player, Human $leatherWorker)
    {
        $this->player = $player;
        $this->leatherWorker = $leatherWorker;
        $npcInventory = InvMenu::create(InvMenu::TYPE_HOPPER);
        $npcInventory->setName("Leather Worker");
        $this->buypoints = VanillaItems::GOLD_NUGGET()
            ->setCustomName(TextFormat::GOLD . "Buy Points")
            ->setLore(["10 Leather", "100 Points"]);

        $this->buyleather = VanillaItems::LEATHER()
            ->setCustomName(TextFormat::DARK_PURPLE . "Buy Leather")
            ->setLore(["500 Points", "10 Leather"]);

        $inventory = $npcInventory->getInventory();
        $inventory->setItem(1, $this->buypoints);
        $inventory->setItem(3, $this->buyleather);
        $this->invMenu = $npcInventory;
    }


    public function openWorkerInventory(?Player $player = null)
    {
        $player = $player ?? $this->player;
        $this->invMenu->send($player);
        $this->invMenu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();

            $data = QuestData::getDatabase($player);
            $points = $data->get('points');
            $leather = $data->get('leather');

            if ($itemClicked->getCustomName() === $this->buypoints->getCustomName()) {
                if ($leather >= 10) {
                    $player->broadcastSound((new ScrapeSound()), [$player]);
                    $data->edit('leather', $leather - 10);
                    $data->edit('points', $points + 100);
                } else {
                    $player->broadcastSound((new ClickSound()), [$player]);
                    $player->sendMessage(TextFormat::RED . "You can not buy this item! You require " . 10 - $leather . " more leather.");
                }
            }

            if ($itemClicked->getCustomName() === $this->buyleather->getCustomName()) {
                if ($points >= 500) {
                    $player->broadcastSound((new ScrapeSound()), [$player]);
                    $data->edit('points', $points - 500);
                    $data->edit('leather', $leather + 10);
                } else {
                    $player->broadcastSound((new ClickSound()), [$player]);
                    $player->sendMessage(TextFormat::RED . "You can not buy this item! You require " . 500 - $points . " more leather.");
                }
            }

            return $transaction->discard();
        });
    }

    public static function create(): HumanNPC
    {

        $leatherworker = new HumanNPC(
            Location::fromObject(new Vector3(9.5, 12, -8.5), Server::getInstance()->getWorldManager()->getWorldByName("lobby"), 45),
            HumanNPC::getSkinFromImage("plugins\lobby\src\lobbynpcs\skins\leatherworker.png")
        );
        $leatherworker->setNameTag("Leather Worker");;

        return $leatherworker;
    }
}
