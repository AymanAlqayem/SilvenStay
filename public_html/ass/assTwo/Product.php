<?php

class Product
{
    private $productId;
    private $productName;
    private $category;
    private $description;
    private $price;
    private $rating;
    private $imageName;
    private $quantity;

    public function __construct($productId, $productName, $category, $description, $price, $rating, $imageName, $quantity)
    {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->category = $category;
        $this->description = $description;
        $this->price = $price;
        $this->rating = $rating;
        $this->imageName = $imageName;
        $this->quantity = $quantity;
    }

    // Getters
    public function getProductId()
    {
        return $this->productId;
    }

    public function getProductName()
    {
        return $this->productName;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    // Setters
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function setProductName($productName)
    {
        $this->productName = $productName;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * displayInTable function that will return product details as HTML table row
     */
    public function displayInTable()
    {
        $productId = htmlspecialchars($this->productId);
        $productName = htmlspecialchars($this->productName);
        $category = htmlspecialchars($this->category);
        $price = htmlspecialchars($this->price);
        $quantity = isset($this->quantity) ? htmlspecialchars($this->quantity) : '';
        $imageSrc = $this->imageName ? "images/" . htmlspecialchars($this->imageName) : "images/default.png";
        $altText = $productName;

        return "
    <tr valign='middle'>
        <td align='center'>
            <img src='$imageSrc' alt='$altText' width='150' height='150' loading='lazy'>
        </td>
        
        <td align='center'><a href='view.php?id=$productId'><b>$productId</b></a></td>
        
        <td align='center'>$productName</td>
        
        <td align='center'>$category</td>
        
        <td align='center'><b>\$$price</b></td>
        
        <td align='center'>$quantity</td>
        
        <td align='center'>
        
            <button type='button'>
                <a href='edit.php?id=$productId'>
                    <img src='images/edit.png' alt='Edit' width='24' height='24'>
                </a>
            </button>
            
              

            <button type='button'>
                <a href='delete.php?id=$productId' onclick=\"return confirm('Are you sure you want to delete this product?')\">
                    <img src='images/trash.png' alt='Delete' width='24' height='24'>
                </a>
            </button>
            
        </td>
    </tr>";
    }

    /**
     * displayProductPage function that will display a specific product details.
     */
    public function displayProductPage()
    {
        $imageSrc = $this->imageName ? "images/" . htmlspecialchars($this->imageName) : "images/default.png";
        $altText = htmlspecialchars($this->productName);
        // Split description into bullet points
        $descriptionLines = array_filter(array_map('trim', explode("\n", $this->description)));
        $descriptionHtml = '';
        foreach ($descriptionLines as $line) {
            $descriptionHtml .= "<li>" . htmlspecialchars($line) . "</li>";
        }

        return "
            <main>
                <img src='$imageSrc' alt='$altText' width='300' height='400'>
                
                <h2>Product ID: " . htmlspecialchars($this->productId) . ", " . htmlspecialchars($this->productName) . "</h2>
                
                <ul>
                    <li>Price: \$" . htmlspecialchars($this->price) . "</li>
                    <li>Category: " . htmlspecialchars($this->category) . "</li>
                    <li>Rating: " . htmlspecialchars($this->rating) . "/5</li>
                
                </ul>
                
                <p>Description:</p>
                <ul>
                    $descriptionHtml
                </ul>
            </main>";
    }
}
