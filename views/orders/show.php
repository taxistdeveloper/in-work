<?php
$isOwner = is_logged_in() && user_id() === (int)$order['client_id'];
$isFreelancer = is_logged_in() && user_role() === 'freelancer';
$isAssigned = is_logged_in() && user_id() === (int)($order['freelancer_id'] ?? 0);
$budget = (float)$order['budget'];

$statusLabels = [
    'open'        => 'Открыт',
    'in_progress' => 'В работе',
    'completed'   => 'Завершён',
    'cancelled'   => 'Отменён',
    'dispute'     => 'Спор',
];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Основной контент -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Шапка заказа -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
                <div class="flex items-start justify-between mb-4">
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full"><?= e($order['category']) ?></span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full <?php
                        echo match($order['status']) {
                            'open'        => 'bg-emerald-50 text-emerald-700',
                            'in_progress' => 'bg-blue-50 text-blue-700',
                            'completed'   => 'bg-gray-100 text-gray-700',
                            'cancelled'   => 'bg-red-50 text-red-700',
                            default       => 'bg-gray-100 text-gray-700',
                        };
                    ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?php
                            echo match($order['status']) {
                                'open'        => 'bg-emerald-500',
                                'in_progress' => 'bg-blue-500',
                                'completed'   => 'bg-gray-500',
                                'cancelled'   => 'bg-red-500',
                                default       => 'bg-gray-500',
                            };
                        ?>"></span>
                        <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-4"><?= e($order['title']) ?></h1>
                <div class="prose prose-gray max-w-none text-gray-600 text-sm leading-relaxed">
                    <?= nl2br(e($order['description'])) ?>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Бюджет</div>
                        <div class="text-lg font-bold text-brand-600"><?= format_money($budget) ?></div>
                    </div>
                    <?php if ($order['final_price']): ?>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Итоговая цена</div>
                            <div class="text-lg font-bold text-gray-900"><?= format_money((float)$order['final_price']) ?></div>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Дедлайн</div>
                        <div class="text-sm font-semibold text-gray-900"><?= e($order['deadline']) ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Создан</div>
                        <div class="text-sm font-semibold text-gray-900"><?= time_ago($order['created_at']) ?></div>
                    </div>
                </div>

                <!-- Действия владельца -->
                <?php if ($isOwner && $order['status'] === 'in_progress'): ?>
                    <?php if (!empty($order['delivered_at'])): ?>
                    <div class="mt-6 pt-6 border-t border-gray-100 rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                        <p class="text-sm font-medium text-emerald-800">Исполнитель сдал работу <?= date('d.m.Y в H:i', strtotime($order['delivered_at'])) ?>.</p>
                        <?php if (!empty($order['delivery_message'])): ?>
                        <p class="text-sm text-emerald-700 mt-2"><?= nl2br(e($order['delivery_message'])) ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-emerald-600 mt-2">Проверьте результат и нажмите «Завершить заказ», чтобы перевести оплату исполнителю.</p>
                    </div>
                    <?php endif; ?>
                    <div class="flex gap-3 mt-6 pt-6 border-t border-gray-100 flex-wrap">
                        <form method="POST" action="<?= url("orders/{$order['id']}/complete") ?>" class="flex-1">
                            <?= csrf_field() ?>
                            <button type="submit" onclick="return confirm('Отметить как выполненный? Средства будут переведены исполнителю.')"
                                    class="w-full py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                                Завершить заказ
                            </button>
                        </form>
                        <form method="POST" action="<?= url("orders/{$order['id']}/cancel") ?>">
                            <?= csrf_field() ?>
                            <button type="submit" onclick="return confirm('Отменить заказ? Средства будут возвращены.')"
                                    class="px-6 py-2.5 border border-red-200 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 transition">
                                Отменить
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Сдача работы исполнителем -->
                <?php if ($isAssigned && $order['status'] === 'in_progress'): ?>
                    <?php if (empty($order['delivered_at'])): ?>
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Сдать работу</h3>
                        <form method="POST" action="<?= url("orders/{$order['id']}/deliver") ?>" class="space-y-3">
                            <?= csrf_field() ?>
                            <textarea name="delivery_message" rows="3" placeholder="Опишите результат или приложите ссылку на работу (необязательно)"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition resize-none"></textarea>
                            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                                Отправить задание заказчику
                            </button>
                        </form>
                    </div>
                    <?php else: ?>
                    <div class="mt-6 pt-6 border-t border-gray-100 rounded-xl bg-amber-50 border border-amber-100 p-4">
                        <p class="text-sm font-medium text-amber-800">Вы сдали работу <?= date('d.m.Y в H:i', strtotime($order['delivered_at'])) ?>.</p>
                        <?php if (!empty($order['delivery_message'])): ?>
                        <p class="text-sm text-amber-700 mt-2"><?= nl2br(e($order['delivery_message'])) ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-amber-600 mt-2">Ожидайте подтверждения заказчика. После этого средства поступят на ваш баланс.</p>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($isOwner && $order['status'] === 'open'): ?>
                    <div class="mt-6 pt-6 border-t border-gray-100 flex flex-wrap gap-3">
                        <a href="<?= url("orders/{$order['id']}/edit") ?>"
                           class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Редактировать заказ
                        </a>
                        <form method="POST" action="<?= url("orders/{$order['id']}/cancel") ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" onclick="return confirm('Отменить заказ?')"
                                    class="px-6 py-2.5 border border-red-200 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 transition">
                                Отменить заказ
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Форма отзыва -->
            <?php if ($canReview): ?>
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Оставить отзыв</h2>
                <form method="POST" action="<?= url('reviews') ?>" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Оценка</label>
                        <div class="flex gap-2" id="ratingStars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button" onclick="setRating(<?= $i ?>)" class="rating-star text-gray-300 hover:text-yellow-400 transition" data-star="<?= $i ?>">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Комментарий (необязательно)</label>
                        <textarea name="comment" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition resize-none"
                                  placeholder="Поделитесь впечатлениями..."></textarea>
                    </div>

                    <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                        Отправить отзыв
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Список откликов -->
            <?php if ($isOwner || $order['status'] !== 'open'): ?>
            <div class="bg-white rounded-2xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Предложения</h2>
                    <span class="text-sm text-gray-500"><?= count($bids) ?> откликов</span>
                </div>
                <?php if (empty($bids)): ?>
                    <div class="px-6 py-12 text-center">
                        <p class="text-gray-400">Пока нет предложений.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-50">
                        <?php foreach ($bids as $bid):
                            $bidRank = get_rank((int)$bid['completed_orders']);
                            $bidStatusLabels = ['pending' => 'Ожидание', 'accepted' => 'Принят', 'rejected' => 'Отклонён'];
                        ?>
                            <div class="p-6 hover:bg-gray-50/50 transition <?= $bid['status'] === 'accepted' ? 'bg-brand-50/50 border-l-4 border-brand-500' : '' ?>">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                                        <?= strtoupper(mb_substr($bid['name'], 0, 2)) ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="<?= url("profile/{$bid['freelancer_id']}") ?>" class="text-sm font-semibold text-gray-900 hover:text-brand-600"><?= e($bid['name']) ?></a>
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-<?= $bidRank['color'] ?>-100 text-<?= $bidRank['color'] ?>-700"><?= $bidRank['name'] ?></span>
                                            <?php if ($bid['status'] !== 'pending'): ?>
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full <?= $bid['status'] === 'accepted' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= $bidStatusLabels[$bid['status']] ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-xs text-gray-500"><?= render_stars((float)$bid['rating']) ?> <?= number_format((float)$bid['rating'], 1) ?></span>
                                            <span class="text-xs text-gray-400"><?= $bid['completed_orders'] ?> заказов</span>
                                        </div>
                                        <?php if ($bid['message']): ?>
                                            <p class="text-sm text-gray-600 mt-2"><?= e($bid['message']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="text-xl font-bold text-gray-900"><?= format_money((float)$bid['amount']) ?></div>
                                        <?php
                                            $diff = (float)$bid['amount'] - $budget;
                                            $pct = $budget > 0 ? round(($diff / $budget) * 100) : 0;
                                        ?>
                                        <div class="text-xs mt-0.5 <?= $diff < 0 ? 'text-emerald-600' : ($diff > 0 ? 'text-red-500' : 'text-gray-400') ?>">
                                            <?= $diff < 0 ? $pct . '%' : ($diff > 0 ? '+' . $pct . '%' : 'По бюджету') ?>
                                        </div>

                                        <?php if ($isOwner && $order['status'] === 'open' && $bid['status'] === 'pending'): ?>
                                            <div class="flex gap-2 mt-3">
                                                <form method="POST" action="<?= url("bids/{$bid['id']}/accept") ?>">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="px-3 py-1.5 bg-brand-600 text-white text-xs font-medium rounded-lg hover:bg-brand-700 transition">Принять</button>
                                                </form>
                                                <form method="POST" action="<?= url("bids/{$bid['id']}/reject") ?>">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-50 transition">Отклонить</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Информация о заказчике -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">Заказчик</h3>
                <a href="<?= url("profile/{$order['client_id']}") ?>" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-sm font-bold">
                        <?= strtoupper(mb_substr($order['client_name'], 0, 2)) ?>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 group-hover:text-brand-600 transition"><?= e($order['client_name']) ?></h4>
                        <div class="text-xs text-gray-500"><?= render_stars((float)$order['client_rating']) ?> <?= number_format((float)$order['client_rating'], 1) ?></div>
                    </div>
                </a>
            </div>

            <!-- Информация об эскроу -->
            <?php if ($escrow && $order['status'] === 'in_progress'): ?>
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h3 class="text-sm font-semibold text-blue-900">Защита эскроу</h3>
                </div>
                <p class="text-sm text-blue-700 mb-2">Средства защищены на эскроу-счёте.</p>
                <div class="text-lg font-bold text-blue-900"><?= format_money((float)$escrow['amount']) ?></div>
                <div class="text-xs text-blue-600 mt-1">Комиссия платформы: <?= format_money((float)$escrow['platform_fee']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Форма отклика (стиль inDrive) -->
            <?php if ($isFreelancer && $order['status'] === 'open' && !$userBid && !$isOwner): ?>
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Предложить свою цену</h3>
                <p class="text-sm text-gray-500 mb-5">Выберите цену или введите свою</p>

                <form method="POST" action="<?= url("orders/{$order['id']}/bid") ?>" id="bidForm">
                    <?= csrf_field() ?>

                    <!-- Кнопки цены (стиль inDrive) -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <button type="button" onclick="setBidAmount(<?= $budget ?>)"
                                class="bid-btn px-3 py-3 border-2 border-gray-200 rounded-xl text-center hover:border-brand-500 hover:bg-brand-50 transition focus:border-brand-500 focus:bg-brand-50">
                            <div class="text-lg font-bold text-gray-900"><?= format_money($budget) ?></div>
                            <div class="text-xs text-gray-500">Цена заказчика</div>
                        </button>
                        <button type="button" onclick="setBidAmount(<?= round($budget * 0.9, 2) ?>)"
                                class="bid-btn px-3 py-3 border-2 border-gray-200 rounded-xl text-center hover:border-emerald-500 hover:bg-emerald-50 transition focus:border-emerald-500 focus:bg-emerald-50">
                            <div class="text-lg font-bold text-emerald-600"><?= format_money($budget * 0.9) ?></div>
                            <div class="text-xs text-emerald-600">-10%</div>
                        </button>
                        <button type="button" onclick="setBidAmount(<?= round($budget * 1.1, 2) ?>)"
                                class="bid-btn px-3 py-3 border-2 border-gray-200 rounded-xl text-center hover:border-amber-500 hover:bg-amber-50 transition focus:border-amber-500 focus:bg-amber-50">
                            <div class="text-lg font-bold text-amber-600"><?= format_money($budget * 1.1) ?></div>
                            <div class="text-xs text-amber-600">+10%</div>
                        </button>
                        <button type="button" onclick="toggleCustomPrice()"
                                class="bid-btn px-3 py-3 border-2 border-gray-200 rounded-xl text-center hover:border-purple-500 hover:bg-purple-50 transition focus:border-purple-500 focus:bg-purple-50">
                            <div class="text-lg font-bold text-purple-600">₸?</div>
                            <div class="text-xs text-purple-600">Своя цена</div>
                        </button>
                    </div>

                    <!-- Поле ввода своей цены -->
                    <div id="customPriceWrap" class="hidden mb-4">
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">₸</span>
                            <input type="number" id="customPriceInput" step="100" min="100" placeholder="Введите свою цену"
                                   class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   oninput="document.getElementById('bidAmount').value = this.value">
                        </div>
                    </div>

                    <input type="hidden" name="amount" id="bidAmount" value="<?= $budget ?>">

                    <!-- Выбранная цена -->
                    <div class="bg-gray-50 rounded-xl p-3 mb-4 text-center" id="selectedPriceDisplay">
                        <span class="text-sm text-gray-500">Ваше предложение: </span>
                        <span class="text-xl font-bold text-brand-600" id="selectedPriceText"><?= format_money($budget) ?></span>
                    </div>

                    <div class="mb-4">
                        <textarea name="message" rows="2" placeholder="Добавьте сообщение (необязательно)"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
                        Отправить предложение
                    </button>
                </form>
            </div>
            <?php elseif ($userBid): ?>
                <?php if ($userBid['status'] === 'rejected'): ?>
                <div class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center">
                    <svg class="w-10 h-10 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold text-red-700">Заказчик отклонил ваш отклик</p>
                    <p class="text-xs text-red-600 mt-1">Ваше предложение <?= format_money((float)$userBid['amount']) ?> не принято.</p>
                </div>
                <?php elseif ($userBid['status'] === 'accepted'): ?>
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center">
                    <svg class="w-10 h-10 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium text-emerald-700">Ваш отклик принят!</p>
                </div>
                <?php else: ?>
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 text-center">
                    <svg class="w-10 h-10 text-amber-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium text-amber-700">Вы отправили отклик на этот заказ.</p>
                    <p class="text-xs text-amber-600 mt-1">Ожидание решения заказчика.</p>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!is_logged_in() && $order['status'] === 'open'): ?>
                <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center">
                    <h3 class="font-semibold text-gray-900 mb-2">Хотите откликнуться?</h3>
                    <p class="text-sm text-gray-500 mb-4">Войдите, чтобы отправить предложение</p>
                    <a href="<?= url('login') ?>" class="inline-block px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">Войти</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function setBidAmount(amount) {
    document.getElementById('bidAmount').value = amount;
    document.getElementById('selectedPriceText').textContent = Math.round(amount).toLocaleString('ru-RU') + ' ₸';
    document.getElementById('customPriceWrap').classList.add('hidden');
    document.querySelectorAll('.bid-btn').forEach(b => {
        b.classList.remove('border-brand-500', 'bg-brand-50', 'border-emerald-500', 'bg-emerald-50', 'border-amber-500', 'bg-amber-50', 'border-purple-500', 'bg-purple-50');
        b.classList.add('border-gray-200');
    });
    event.target.closest('.bid-btn').classList.remove('border-gray-200');
    event.target.closest('.bid-btn').classList.add('border-brand-500', 'bg-brand-50');
}

function toggleCustomPrice() {
    const wrap = document.getElementById('customPriceWrap');
    wrap.classList.toggle('hidden');
    if (!wrap.classList.contains('hidden')) {
        document.getElementById('customPriceInput').focus();
    }
    document.querySelectorAll('.bid-btn').forEach(b => {
        b.classList.remove('border-brand-500', 'bg-brand-50', 'border-emerald-500', 'bg-emerald-50', 'border-amber-500', 'bg-amber-50', 'border-purple-500', 'bg-purple-50');
        b.classList.add('border-gray-200');
    });
}

document.getElementById('customPriceInput')?.addEventListener('input', function() {
    const val = parseFloat(this.value) || 0;
    document.getElementById('bidAmount').value = val;
    document.getElementById('selectedPriceText').textContent = Math.round(val).toLocaleString('ru-RU') + ' ₸';
});

function setRating(stars) {
    document.getElementById('ratingInput').value = stars;
    document.querySelectorAll('.rating-star').forEach((el, i) => {
        el.classList.toggle('text-yellow-400', i < stars);
        el.classList.toggle('text-gray-300', i >= stars);
    });
}
</script>
