<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escape output — պաշտպանում է XSS-ից
 * Օգտագործում՝ <?= e($variable) ?>
 */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

class Product {
    public $pdo;
    public $id;
    public $name;
    public $stock;
    public $price;
    public $category;
    public $discount;
    public $description;
    

    public function __construct($pdo, $productId, $productName, $productStock, $productPrice, $productCategory, $productDiscount = 0, $productDescription = '') {
        $this->pdo = $pdo;
        $this->id = $productId;
        $this->name = $productName;
        $this->stock = $productStock;
        $this->price = $productPrice;
        $this->category = $productCategory;
        $this->discount = $productDiscount;
        $this->description = $productDescription;
    }

    public function choise($quantity = 1) {
        if ($quantity <= 0) {
            return "Քանակը պետք է լինի 0-ից մեծ։\n";
        }
        if ($this->stock >= $quantity) {
            $defaultPrice = $this->price * $quantity;
            $productTotal = ($this->price - $this->discount) * $quantity;
            $lineDiscount = $this->discount * $quantity;

            return "{$this->name} ({$this->category}) ավելացվել է զամբյուղ: Քանակը՝ {$quantity} հատ, Ապրանքի արժեքը՝ {$defaultPrice} Դ․, Զեղչ՝ {$lineDiscount} Դ․, Զամբյուղի արժեքը զեղչվելուց հետո՝ {$productTotal} Դ․\n";
        } else {
            return "Ներեցեք, այս պահին պահեստում առկա է ընդամենը {$this->stock} հատ։\n";
        }
    }

    public function __sleep() {
    return ['id', 'name', 'stock', 'price', 'category', 'discount', 'description'];
}

public function buy($quantity, $pdo) {

    if ($quantity <= 0) {
        return "Քանակը պետք է լինի 0-ից մեծ։\n";
    }

    if ($this->stock >= $quantity) {
        $defaultPrice = $this->price * $quantity;
        $productTotal = ($this->price - $this->discount) * $quantity;
        $lineDiscount = $this->discount * $quantity;

        // 1. Թարմացնում ենք session-ի stock-ը (ինչպես նախկինում)
        $this->stock -= $quantity;

        // 2. Թարմացնում ենք DB-ի stock-ը ✅
        $stmt = $this->pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$quantity, $this->id]);

        return "Գնվեց {$quantity} հատ {$this->name}, Արժեքը՝ {$defaultPrice} Դ․, Զեղչ՝ {$lineDiscount} Դ․, Վճարման արժեքը զեղչվելուց հետո՝ {$productTotal} Դ․\n";
    }

    // ← Ավելացրինք else, որ դատարկ չմնա (քո նշած խնդիրը)
    return "Ներեցեք, պահեստում բավարար քանակ չկա։\n";
}

    public function canChoose($quantity = 1) {
        if ($quantity <= 0) {
            return false;
        }
        if ($quantity > $this->stock) {
            return false;
        }
        return true;
    }

    public function hasMassiveDiscount() {
        if ($this->price == 0) {
            return false;
        }
        $discountPercent = ($this->discount / $this->price) * 100;
        return $discountPercent >= 18;
    }

    public function hasSuperMassiveDiscount() {
        if ($this->price == 0) {
            return false;
        }
        $discountPercent = ($this->discount / $this->price) * 100;
        return $discountPercent >= 30;
    }

    public function getDiscountPercent() {
        if ($this->price == 0) {
            return 0;
        }
        return round(($this->discount / $this->price) * 100);
    }
}

class Cart {
    public $products = [];

    public function add($product, $quantity = 1) {
        foreach ($this->products as $key => $item) {
            // Ստուգում ենք ID-ով, որ նույնանուն ապրանքները չխառնվեն
            if ($item['product']->id === $product->id) {
                $newQuantity = $item['quantity'] + $quantity;
                if ($newQuantity > $product->stock) {
                    return "Ներողություն, պահեստում կա միայն {$product->stock} հատ։ Դուք արդեն ունեք {$item['quantity']} հատ զամբյուղում։\n";
                }
                $this->products[$key]['quantity'] = $newQuantity;
                return "{$product->name}-ի քանակը թարմացվեց՝ {$newQuantity} հատ։\n";
            }
        }
        
        if (!$product->canChoose($quantity)) {
            return "Ներողություն {$quantity} հատ {$product->name} հնարավոր չէ ավելացնել, պահեստում առկա է {$product->stock} հատ։\n";
        }
        
        $this->products[] = [
            'product'  => $product,
            'quantity' => $quantity
        ];
        
        return $product->choise($quantity);
    }

    public function updateQuantity($product, $newQuantity) {
        if ($newQuantity <= 0) {
            return "քանակը պետք է մեծ լինի 0";
        }
        if ($newQuantity > $product->stock) {
            return "Պահեստում չկա բավարար ապրանք";
        }
        foreach ($this->products as $key => $item) {
            if ($item['product']->id === $product->id) {
                $this->products[$key]['quantity'] = $newQuantity;
                return "{$product->name}-ի քանակը թարմացվեց՝ {$newQuantity} հատ։\n";
            }
        }

        return "{$product->name}-ը զամբյուղում չկա։\n";
    }

        public function remove($product) {
        foreach ($this->products as $key => $item) {
            if ($item['product']->id === $product->id) {
                unset($this->products[$key]);
                return "{$product->name}-ը հանվեց զամբյուղից։\n";
            }
        }
        return "{$product->name}-ը զամբյուղում չկա։\n";
    }
    
    public function checkout($pdo) {
    foreach ($this->products as $item) {
        $product  = $item['product'];
        $quantity = $item['quantity'];
        echo $product->buy($quantity, $pdo); // <--- Փոխանցում ենք $pdo-ն
    }
    $total = $this->getTotal();
    $this->products = [];
    return $total;
}

    public function getTotalDefault() {
        $sum = 0;
        foreach ($this->products as $item) {
            $product  = $item['product'];
            $quantity = $item['quantity'];
            $sum += $product->price * $quantity;
        }
        return $sum;
    }

    public function getTotalDiscount() {
        $sum = 0;
        foreach ($this->products as $item) {
            $product  = $item['product'];
            $quantity = $item['quantity'];
            $sum += $product->discount * $quantity;
        }
        return $sum;
    }

    public function getTotal() {
        $sum = 0;
        foreach ($this->products as $item) {
            $product  = $item['product'];
            $quantity = $item['quantity'];
            $sum += ($product->price - $product->discount) * $quantity;
        }
        return $sum;
    }

}

// 1. Միացնում ենք տվյալների բազայի կապը
require_once 'db.php';

// 2. Բազայից կարդում ենք ապրանքները
$stmt = $pdo->query("SELECT * FROM products");
$productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Մաքրում ենք կամ ստեղծում սեսիայի զանգվածը և լցնում բազայի տվյալներով
$_SESSION['products'] = [];
foreach ($productsData as $data) {
    $product = new Product(
        $pdo,
        $data['id'],
        $data['name'],
        $data['stock'],
        $data['price'],
        $data['category'],
        $data['discount'],
        $data['description']
    );
    // Խմբավորում ենք ըստ կատեգորիաների և պահում հենց SESSIONS-ում
    $_SESSION['products'][$data['category']][] = $product;
}

// 4. Հղում ենք տալիս session-ին, որ index.php-ն և մյուս էջերը տեսնեն
$products = &$_SESSION['products'];

if (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
} else {
    $cart = new Cart();            
    $_SESSION['cart'] = $cart; 
}

?>