import 'package:flutter/material.dart';

import '../ui/components/components.dart';
import '../ui/theme/app_theme.dart';

/// Центр помощи, политика конфиденциальности и условия использования.
class HelpCenterScreen extends StatelessWidget {
  const HelpCenterScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: CustomScrollView(
        slivers: [
          _StaticHeroSliver(
            title: 'Центр помощи',
            subtitle: 'Ответы на частые вопросы о заказах, оплате и безопасности.',
            icon: Icons.help_outline_rounded,
          ),
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                _HelpCard(
                  icon: Icons.assignment_outlined,
                  title: 'Как создать заказ?',
                  child: const _NumberedSteps([
                    'Перейдите в личный кабинет',
                    'Нажмите «Создать заказ»',
                    'Заполните описание, цену и срок',
                    'Опубликуйте заказ',
                  ]),
                ),
                _HelpCard(
                  icon: Icons.groups_outlined,
                  title: 'Как выбрать исполнителя?',
                  intro: 'Вы можете:',
                  child: const _CheckList([
                    'Выбрать исполнителя из списка специалистов',
                    'Или дождаться откликов на заказ',
                  ]),
                ),
                _HelpCard(
                  icon: Icons.payments_outlined,
                  title: 'Как работает оплата?',
                  child: const _CheckList([
                    'Средства резервируются на платформе',
                    'После выполнения работы переводятся исполнителю',
                  ]),
                ),
                _HelpCard(
                  icon: Icons.star_outline_rounded,
                  title: 'Как оставить отзыв?',
                  intro: 'После завершения заказа:',
                  child: const _CheckList([
                    'Перейдите в раздел «Мои сделки»',
                    'Выберите заказ',
                    'Нажмите «Оставить отзыв»',
                  ]),
                ),
                _HelpCard(
                  icon: Icons.balance_outlined,
                  title: 'Что делать при споре?',
                  child: const _CheckList([
                    'Свяжитесь с поддержкой',
                    'Опишите проблему',
                    'Предоставьте доказательства',
                  ]),
                ),
                _HelpCard(
                  icon: Icons.account_balance_wallet_outlined,
                  title: 'Как пополнить баланс?',
                  child: const _CheckList([
                    'Перейдите в раздел «Баланс»',
                    'Выберите сумму',
                    'Оплатите удобным способом',
                  ]),
                ),
                _HelpCard(
                  icon: Icons.work_outline_rounded,
                  title: 'Как стать исполнителем?',
                  child: const _CheckList([
                    'Зарегистрируйтесь',
                    'Заполните профиль',
                    'Укажите навыки',
                    'Начните откликаться на заказы',
                  ]),
                ),
                _SecurityCard(
                  child: const _CheckList(
                    [
                      'Не переводите деньги вне платформы',
                      'Не передавайте личные данные',
                    ],
                    dotColor: Color(0xFFF59E0B),
                  ),
                ),
                const _SupportGradientCard(),
                const SizedBox(height: 8),
                _DocLinkTile(
                  icon: Icons.privacy_tip_outlined,
                  title: 'Политика конфиденциальности',
                  subtitle: 'Как мы обрабатываем данные',
                  onTap: () => Navigator.of(context).push(
                    MaterialPageRoute<void>(builder: (_) => const PrivacyPolicyScreen()),
                  ),
                ),
                _DocLinkTile(
                  icon: Icons.description_outlined,
                  title: 'Условия использования',
                  subtitle: 'Правила платформы',
                  onTap: () => Navigator.of(context).push(
                    MaterialPageRoute<void>(builder: (_) => const TermsOfUseScreen()),
                  ),
                ),
                const SizedBox(height: 24),
              ]),
            ),
          ),
        ],
      ),
    );
  }
}

class _StaticHeroSliver extends StatelessWidget {
  const _StaticHeroSliver({
    required this.title,
    required this.subtitle,
    required this.icon,
  });

  final String title;
  final String subtitle;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return SliverAppBar(
      expandedHeight: 188,
      pinned: true,
      backgroundColor: const Color(0xFF047857),
      foregroundColor: Colors.white,
      iconTheme: const IconThemeData(color: Colors.white),
      flexibleSpace: FlexibleSpaceBar(
        titlePadding: const EdgeInsetsDirectional.only(start: 56, bottom: 14),
        title: Text(
          title,
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w800,
            fontSize: 17,
            shadows: [Shadow(blurRadius: 8, color: Colors.black26)],
          ),
        ),
        background: Stack(
          fit: StackFit.expand,
          children: [
            DecoratedBox(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    Color(0xFF1F2937),
                    Color(0xFF064E3B),
                    Color(0xFF047857),
                  ],
                ),
              ),
            ),
            Positioned(
              top: -40,
              left: -30,
              child: Container(
                width: 140,
                height: 140,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withValues(alpha:0.07),
                ),
              ),
            ),
            Positioned(
              bottom: -24,
              right: -16,
              child: Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: const Color(0xFF34D399).withValues(alpha:0.18),
                ),
              ),
            ),
            SafeArea(
              bottom: false,
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 12, 20, 0),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      width: 52,
                      height: 52,
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha:0.14),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: Colors.white.withValues(alpha:0.22)),
                      ),
                      child: Icon(icon, color: const Color(0xFF6EE7B7), size: 28),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(
                          subtitle,
                          style: TextStyle(
                            color: Colors.white.withValues(alpha:0.88),
                            height: 1.4,
                            fontSize: 15,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _HelpCard extends StatelessWidget {
  const _HelpCard({
    required this.icon,
    required this.title,
    required this.child,
    this.intro,
  });

  final IconData icon;
  final String title;
  final String? intro;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Material(
        color: Colors.white,
        elevation: 0,
        shadowColor: Colors.transparent,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: BorderSide(color: Colors.grey.shade200),
        ),
        child: Padding(
            padding: const EdgeInsets.all(18),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [
                        AppColors.brand500.withValues(alpha:0.2),
                        const Color(0xFFECFDF5),
                      ],
                    ),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(icon, color: AppColors.brand600, size: 24),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        style: Theme.of(context).textTheme.titleSmall?.copyWith(
                              fontWeight: FontWeight.w800,
                              color: Colors.grey.shade900,
                            ),
                      ),
                      if (intro != null) ...[
                        const SizedBox(height: 6),
                        Text(
                          intro!,
                          style: TextStyle(color: Colors.grey.shade600, fontSize: 13),
                        ),
                      ],
                      const SizedBox(height: 10),
                      child,
                    ],
                  ),
                ),
              ],
            ),
          ),
      ),
    );
  }
}

class _SecurityCard extends StatelessWidget {
  const _SecurityCard({required this.child});

  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white,
              const Color(0xFFFFF7ED).withValues(alpha: 0.9),
            ],
          ),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFFFDE68A).withValues(alpha: 0.8)),
        ),
        padding: const EdgeInsets.all(18),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: const Color(0xFFFEF3C7),
                borderRadius: BorderRadius.circular(14),
              ),
              child: const Icon(Icons.lock_outline_rounded, color: Color(0xFFD97706), size: 24),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Безопасность',
                    style: Theme.of(context).textTheme.titleSmall?.copyWith(
                          fontWeight: FontWeight.w800,
                          color: Colors.grey.shade900,
                        ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Никогда:',
                    style: TextStyle(
                      color: const Color(0xFF92400E),
                      fontWeight: FontWeight.w600,
                      fontSize: 13,
                    ),
                  ),
                  const SizedBox(height: 8),
                  child,
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _SupportGradientCard extends StatelessWidget {
  const _SupportGradientCard();

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Color(0xFF059669),
            Color(0xFF047857),
            Color(0xFF065F46),
          ],
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF047857).withValues(alpha: 0.35),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      padding: const EdgeInsets.all(20),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(Icons.mail_outline_rounded, color: Color(0xFF6EE7B7), size: 30),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Поддержка',
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w800,
                    fontSize: 20,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Напишите нам — ответим в рабочее время',
                  style: TextStyle(color: Colors.white.withValues(alpha: 0.85), fontSize: 13),
                ),
                const SizedBox(height: 10),
                const SelectableText(
                  'support@in-work.kz',
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                    fontSize: 17,
                    decoration: TextDecoration.underline,
                    decorationColor: Colors.white54,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _DocLinkTile extends StatelessWidget {
  const _DocLinkTile({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Material(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(18),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Row(
              children: [
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    color: AppColors.brand500.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(icon, color: AppColors.brand600),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                      const SizedBox(height: 2),
                      Text(subtitle, style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
                    ],
                  ),
                ),
                Icon(Icons.chevron_right_rounded, color: Colors.grey.shade400),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _NumberedSteps extends StatelessWidget {
  const _NumberedSteps(this.steps);

  final List<String> steps;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        for (var i = 0; i < steps.length; i++)
          Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 28,
                  height: 28,
                  alignment: Alignment.center,
                  decoration: BoxDecoration(
                    color: AppColors.brand600,
                    borderRadius: BorderRadius.circular(8),
                    boxShadow: [
                      BoxShadow(
                        color: AppColors.brand600.withValues(alpha: 0.35),
                        blurRadius: 6,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: Text(
                    '${i + 1}',
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 13),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.only(top: 4),
                    child: Text(
                      steps[i],
                      style: TextStyle(color: Colors.grey.shade700, height: 1.4),
                    ),
                  ),
                ),
              ],
            ),
          ),
      ],
    );
  }
}

class _CheckList extends StatelessWidget {
  const _CheckList(this.items, {this.dotColor});

  final List<String> items;
  final Color? dotColor;

  @override
  Widget build(BuildContext context) {
    final c = dotColor ?? AppColors.brand500;
    return Column(
      children: items.map((e) {
        return Padding(
          padding: const EdgeInsets.only(bottom: 8),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.only(top: 6),
                child: Container(
                  width: 6,
                  height: 6,
                  decoration: BoxDecoration(color: c, shape: BoxShape.circle),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(child: Text(e, style: TextStyle(color: Colors.grey.shade700, height: 1.4))),
            ],
          ),
        );
      }).toList(),
    );
  }
}

class PrivacyPolicyScreen extends StatelessWidget {
  const PrivacyPolicyScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: CustomScrollView(
        slivers: [
          const _StaticHeroSliver(
            title: 'Политика конфиденциальности',
            subtitle: 'Как мы собираем, храним и защищаем ваши данные на платформе inWork.',
            icon: Icons.shield_outlined,
          ),
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 32),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                _IntroBanner(
                  child: Text(
                    'Настоящая Политика конфиденциальности регулирует порядок сбора, хранения и использования персональных данных '
                    'пользователей платформы inWork (далее — «Платформа»).',
                    style: TextStyle(color: Colors.grey.shade800, height: 1.5),
                  ),
                ),
                _LegalNumberedCard(
                  n: 1,
                  title: 'Общие положения',
                  paragraphs: const [
                    'Используя Платформу, пользователь соглашается с условиями настоящей Политики.',
                    'Платформа обрабатывает персональные данные в соответствии с законодательством Республики Казахстан.',
                  ],
                ),
                _LegalNumberedCard(
                  n: 2,
                  title: 'Какие данные мы собираем',
                  intro: 'Мы можем собирать следующие данные:',
                  bullets: const [
                    'Имя и контактная информация (email, телефон)',
                    'Данные профиля (навыки, фото, описание)',
                    'Платежные данные (в обезличенном виде через платежные системы)',
                    'История заказов и откликов',
                    'IP-адрес, cookies, данные устройства',
                  ],
                ),
                _LegalNumberedCard(
                  n: 3,
                  title: 'Цели обработки данных',
                  intro: 'Данные используются для:',
                  bullets: const [
                    'Регистрации и авторизации пользователей',
                    'Обеспечения работы платформы',
                    'Проведения сделок между пользователями',
                    'Улучшения качества сервиса',
                    'Предотвращения мошенничества',
                    'Рассылки уведомлений',
                  ],
                ),
                _LegalNumberedCard(
                  n: 4,
                  title: 'Передача данных третьим лицам',
                  intro: 'Мы не передаем персональные данные третьим лицам, за исключением:',
                  bullets: const [
                    'Платежных систем (для обработки платежей)',
                    'Государственных органов (по законному требованию)',
                    'Технических подрядчиков (хостинг, аналитика)',
                  ],
                ),
                _LegalNumberedCard(
                  n: 5,
                  title: 'Хранение данных',
                  paragraphs: const [
                    'Данные хранятся столько, сколько необходимо для выполнения целей обработки, либо в соответствии с законодательством.',
                  ],
                ),
                _LegalNumberedCard(
                  n: 6,
                  title: 'Защита данных',
                  intro: 'Мы принимаем все разумные меры для защиты данных:',
                  bullets: const ['Шифрование', 'Контроль доступа', 'Мониторинг активности'],
                ),
                _LegalNumberedCard(
                  n: 7,
                  title: 'Права пользователя',
                  intro: 'Пользователь имеет право:',
                  bullets: const [
                    'Запросить доступ к своим данным',
                    'Изменить или удалить данные',
                    'Отозвать согласие на обработку',
                  ],
                ),
                _LegalNumberedCard(
                  n: 8,
                  title: 'Cookies',
                  intro: 'Платформа использует cookies для:',
                  bullets: const ['Авторизации', 'Аналитики', 'Персонализации'],
                ),
                _LegalNumberedCard(
                  n: 9,
                  title: 'Изменения политики',
                  paragraphs: const [
                    'Мы можем обновлять Политику. Новая версия вступает в силу с момента публикации.',
                  ],
                ),
                _LegalContactCard(
                  n: 10,
                  intro: 'По вопросам обработки данных:',
                ),
                const SizedBox(height: 8),
                _LegalNavRow(
                  leftLabel: 'Условия',
                  onLeft: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).push(
                      MaterialPageRoute<void>(builder: (_) => const TermsOfUseScreen()),
                    );
                  },
                  rightLabel: 'Центр помощи',
                  onRight: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).push(
                      MaterialPageRoute<void>(builder: (_) => const HelpCenterScreen()),
                    );
                  },
                ),
              ]),
            ),
          ),
        ],
      ),
    );
  }
}

class TermsOfUseScreen extends StatelessWidget {
  const TermsOfUseScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: CustomScrollView(
        slivers: [
          const _StaticHeroSliver(
            title: 'Условия использования',
            subtitle: 'Правила работы с платформой inWork для заказчиков и исполнителей.',
            icon: Icons.description_outlined,
          ),
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 32),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                _IntroBanner(
                  child: Text(
                    'Настоящие Условия регулируют использование платформы inWork.',
                    style: TextStyle(color: Colors.grey.shade800, height: 1.5),
                  ),
                ),
                _LegalNumberedCard(
                  n: 1,
                  title: 'Общие положения',
                  paragraphs: const [
                    'Платформа предоставляет сервис для взаимодействия заказчиков и исполнителей.',
                    'Платформа не является стороной сделки между пользователями.',
                  ],
                ),
                _LegalNumberedCard(
                  n: 2,
                  title: 'Регистрация',
                  intro: 'Пользователь обязуется:',
                  bullets: const [
                    'Указывать достоверные данные',
                    'Не передавать аккаунт третьим лицам',
                    'Обеспечивать безопасность доступа',
                  ],
                ),
                _RolesCard(),
                _LegalNumberedCard(
                  n: 4,
                  title: 'Платежи',
                  intro: 'Платформа может использовать систему безопасных сделок (escrow):',
                  bullets: const [
                    'Средства резервируются',
                    'Переводятся исполнителю после подтверждения',
                  ],
                  paragraphsAfterBullets: const ['Платформа может удерживать комиссию.'],
                ),
                _LegalNumberedCard(
                  n: 5,
                  title: 'Ответственность',
                  intro: 'Платформа:',
                  bullets: const [
                    'Не гарантирует качество услуг',
                    'Не несет ответственности за действия пользователей',
                  ],
                  paragraphsAfterBullets: const ['Пользователи несут ответственность за свои действия.'],
                ),
                _LegalNumberedCard(
                  n: 6,
                  title: 'Запрещенные действия',
                  intro: 'Запрещено:',
                  bullets: const [
                    'Мошенничество',
                    'Обман пользователей',
                    'Использование платформы в незаконных целях',
                    'Обход комиссии платформы',
                  ],
                  bulletIcon: Icons.close_rounded,
                  bulletColor: const Color(0xFFF87171),
                ),
                _LegalNumberedCard(
                  n: 7,
                  title: 'Блокировка аккаунта',
                  intro: 'Платформа вправе:',
                  bullets: const ['Ограничить доступ', 'Заблокировать аккаунт'],
                  paragraphsAfterBullets: const ['При нарушении условий.'],
                ),
                _LegalNumberedCard(
                  n: 8,
                  title: 'Отзывы и рейтинг',
                  paragraphs: const [
                    'Пользователи могут оставлять отзывы.',
                    'Платформа вправе модерировать их.',
                  ],
                ),
                _LegalNumberedCard(
                  n: 9,
                  title: 'Изменения условий',
                  paragraphs: const ['Платформа может изменять условия без предварительного уведомления.'],
                ),
                _LegalContactCard(n: 10, intro: null),
                const SizedBox(height: 8),
                _LegalNavRow(
                  leftLabel: 'Политика',
                  onLeft: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).push(
                      MaterialPageRoute<void>(builder: (_) => const PrivacyPolicyScreen()),
                    );
                  },
                  rightLabel: 'Центр помощи',
                  onRight: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).push(
                      MaterialPageRoute<void>(builder: (_) => const HelpCenterScreen()),
                    );
                  },
                ),
              ]),
            ),
          ),
        ],
      ),
    );
  }
}

class _IntroBanner extends StatelessWidget {
  const _IntroBanner({required this.child});

  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              AppColors.brand500.withValues(alpha: 0.12),
              const Color(0xFFECFDF5),
            ],
          ),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: AppColors.brand500.withValues(alpha: 0.25)),
        ),
        child: child,
      ),
    );
  }
}

class _LegalNumberedCard extends StatelessWidget {
  const _LegalNumberedCard({
    required this.n,
    required this.title,
    this.intro,
    this.paragraphs,
    this.bullets,
    this.paragraphsAfterBullets,
    this.bulletIcon,
    this.bulletColor,
  });

  final int n;
  final String title;
  final String? intro;
  final List<String>? paragraphs;
  final List<String>? bullets;
  final List<String>? paragraphsAfterBullets;
  final IconData? bulletIcon;
  final Color? bulletColor;

  @override
  Widget build(BuildContext context) {
    final bodyStyle = TextStyle(color: Colors.grey.shade700, height: 1.45);
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: AppCard(
        padding: const EdgeInsets.all(18),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 40,
              height: 40,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Color(0xFF10B981), Color(0xFF047857)],
                ),
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.brand600.withValues(alpha: 0.35),
                    blurRadius: 8,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Text(
                '$n',
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 16),
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
                  ),
                  const SizedBox(height: 8),
                  if (intro != null) Text(intro!, style: bodyStyle.copyWith(fontSize: 13, color: Colors.grey.shade600)),
                  if (intro != null && (paragraphs != null || bullets != null)) const SizedBox(height: 6),
                  if (paragraphs != null)
                    ...paragraphs!.map(
                      (p) => Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: Text(p, style: bodyStyle),
                      ),
                    ),
                  if (bullets != null)
                    ...bullets!.map(
                      (e) => Padding(
                        padding: const EdgeInsets.only(bottom: 6),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Icon(
                              bulletIcon ?? Icons.check_rounded,
                              size: 20,
                              color: bulletColor ?? AppColors.brand500,
                            ),
                            const SizedBox(width: 8),
                            Expanded(child: Text(e, style: bodyStyle)),
                          ],
                        ),
                      ),
                    ),
                  if (paragraphsAfterBullets != null) ...[
                    const SizedBox(height: 4),
                    ...paragraphsAfterBullets!.map(
                      (p) => Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(p, style: bodyStyle),
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _RolesCard extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: AppCard(
        padding: const EdgeInsets.all(18),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 40,
              height: 40,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF10B981), Color(0xFF047857)],
                ),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Text('3', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800)),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Роли пользователей', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
                  const SizedBox(height: 12),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        child: _RoleBox(
                          label: 'Заказчик',
                          color: const Color(0xFFDBEAFE),
                          textColor: const Color(0xFF1E40AF),
                          items: const ['Создает задания', 'Выбирает исполнителей', 'Оплачивает услуги'],
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: _RoleBox(
                          label: 'Исполнитель',
                          color: const Color(0xFFD1FAE5),
                          textColor: const Color(0xFF065F46),
                          items: const ['Откликается на задания', 'Выполняет работу', 'Получает оплату'],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _RoleBox extends StatelessWidget {
  const _RoleBox({
    required this.label,
    required this.color,
    required this.textColor,
    required this.items,
  });

  final String label;
  final Color color;
  final Color textColor;
  final List<String> items;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: textColor.withValues(alpha: 0.15)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: TextStyle(fontWeight: FontWeight.w800, color: textColor, fontSize: 13)),
          const SizedBox(height: 6),
          ...items.map(
            (line) => Padding(
              padding: const EdgeInsets.only(bottom: 4),
              child: Text(
                '• $line',
                style: TextStyle(color: textColor.withValues(alpha: 0.9), fontSize: 12, height: 1.35),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _LegalContactCard extends StatelessWidget {
  const _LegalContactCard({required this.n, required this.intro});

  final int n;
  final String? intro;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 4),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF1F2937), Color(0xFF064E3B)],
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.2),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 40,
            height: 40,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Text(
              '$n',
              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 16),
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Контакты',
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 17),
                ),
                if (intro != null) ...[
                  const SizedBox(height: 6),
                  Text(
                    intro!,
                    style: TextStyle(color: Colors.white.withValues(alpha: 0.75), fontSize: 13),
                  ),
                ],
                const SizedBox(height: 8),
                const SelectableText(
                  'support@in-work.kz',
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                    fontSize: 16,
                    decoration: TextDecoration.underline,
                    decorationColor: Colors.white54,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _LegalNavRow extends StatelessWidget {
  const _LegalNavRow({
    required this.leftLabel,
    required this.onLeft,
    required this.rightLabel,
    required this.onRight,
  });

  final String leftLabel;
  final VoidCallback onLeft;
  final String rightLabel;
  final VoidCallback onRight;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: OutlinedButton(
            onPressed: onLeft,
            style: OutlinedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            ),
            child: Text(leftLabel),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: FilledButton(
            onPressed: onRight,
            style: FilledButton.styleFrom(
              backgroundColor: AppColors.brand600,
              padding: const EdgeInsets.symmetric(vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            ),
            child: Text(rightLabel),
          ),
        ),
      ],
    );
  }
}
