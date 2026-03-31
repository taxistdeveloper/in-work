class OrderModel {
  const OrderModel({
    required this.id,
    required this.title,
    required this.description,
    required this.category,
    required this.budget,
    required this.deadline,
    required this.status,
  });

  final int id;
  final String title;
  final String description;
  final String category;
  final double budget;
  final String deadline;
  final String status;

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    return OrderModel(
      id: json['id'] as int,
      title: json['title'] as String,
      description: json['description'] as String? ?? '',
      category: json['category'] as String? ?? '',
      budget: (json['budget'] as num).toDouble(),
      deadline: json['deadline'] as String? ?? '',
      status: json['status'] as String? ?? 'open',
    );
  }
}

