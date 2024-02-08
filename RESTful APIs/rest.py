from flask import Flask, request, jsonify

app = Flask(__name__)

# Sample data (in-memory database)
items = []

# Create a new item
@app.route('/items', methods=['POST'])
def create_item():
    data = request.get_json()
    items.append(data)
    return jsonify(data), 201

# Get all items
@app.route('/items', methods=['GET'])
def get_items():
    return jsonify(items)

# Get a specific item by ID
@app.route('/items/<int:item_id>', methods=['GET'])
def get_item(item_id):
    if item_id < len(items):
        return jsonify(items[item_id])
    else:
        return "Item not found", 404

# Update an item by ID
@app.route('/items/<int:item_id>', methods=['PUT'])
def update_item(item_id):
    if item_id < len(items):
        data = request.get_json()
        items[item_id] = data
        return jsonify(data)
    else:
        return "Item not found", 404

# Delete an item by ID
@app.route('/items/<int:item_id>', methods=['DELETE'])
def delete_item(item_id):
    if item_id < len(items):
        deleted_item = items.pop(item_id)
        return jsonify(deleted_item)
    else:
        return "Item not found", 404

if __name__ == '__main__':
    app.run(debug=True)
