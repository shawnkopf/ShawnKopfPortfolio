import express, { Request, Response } from 'express';

const app = express();
const port = 3000;

interface Item {
    name: string;
    // Add other properties as needed
}

const items: Item[] = [];

app.use(express.json());

// Create a new item
app.post('/items', (req: Request, res: Response) => {
    const item: Item = req.body;
    items.push(item);
    res.status(201).json(item);
});

// Get all items
app.get('/items', (req: Request, res: Response) => {
    res.json(items);
});

// Get a specific item by ID
app.get('/items/:id', (req: Request, res: Response) => {
    const itemId: number = parseInt(req.params.id);
    if (itemId < items.length) {
        res.json(items[itemId]);
    } else {
        res.status(404).send('Item not found');
    }
});

// Update an item by ID
app.put('/items/:id', (req: Request, res: Response) => {
    const itemId: number = parseInt(req.params.id);
    if (itemId < items.length) {
        const updatedItem: Item = req.body;
        items[itemId] = updatedItem;
        res.json(updatedItem);
    } else {
        res.status(404).send('Item not found');
    }
});

// Delete an item by ID
app.delete('/items/:id', (req: Request, res: Response) => {
    const itemId: number = parseInt(req.params.id);
    if (itemId < items.length) {
        const deletedItem = items.splice(itemId, 1)[0];
        res.json(deletedItem);
    } else {
        res.status(404).send('Item not found');
    }
});

app.listen(port, () => {
    console.log(`Server is running on port ${port}`);
});
