import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.web.bind.annotation.*;

import java.util.ArrayList;
import java.util.List;

@SpringBootApplication
public class SimpleRestApiApplication {

    public static void main(String[] args) {
        SpringApplication.run(SimpleRestApiApplication.class, args);
    }
}

@RestController
@RequestMapping("/items")
class ItemController {
    private List<Item> items = new ArrayList<>();

    @PostMapping
    public Item createItem(@RequestBody Item item) {
        items.add(item);
        return item;
    }

    @GetMapping
    public List<Item> getItems() {
        return items;
    }

    @GetMapping("/{id}")
    public Item getItem(@PathVariable int id) {
        if (id < items.size()) {
            return items.get(id);
        } else {
            throw new NotFoundException();
        }
    }

    @PutMapping("/{id}")
    public Item updateItem(@PathVariable int id, @RequestBody Item item) {
        if (id < items.size()) {
            items.set(id, item);
            return item;
        } else {
            throw new NotFoundException();
        }
    }

    @DeleteMapping("/{id}")
    public Item deleteItem(@PathVariable int id) {
        if (id < items.size()) {
            return items.remove(id);
        } else {
            throw new NotFoundException();
        }
    }
}

class Item {
    private String name;
    // Add other properties as needed

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }
}

class NotFoundException extends RuntimeException {
    // Custom exception for handling 404 errors
}
