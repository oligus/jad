# JAD

[<< Back](../README.md)

## Validation

Validation is supported by default using the [Symfony](https://symfony.com/doc/current/validation.html) validation library.

Simplest way to validate your fields is to use assertion annotations.

#### Examples

Assert that `$firstName` is not empty:

```php
use Symfony\Component\Validator\Constraints as Assert;
...
/**
 * @ORM\Column(name="FirstName", type="string", length=40)
 * @Assert\NotBlank()
 */
protected $firstName;
```

Assert `$email` is not empty:

```php
/**
 * @ORM\Column(name="email", type="string", length=150)
 * @Assert\Email(message="Not valid email")
 */
protected $email;
```

Read more about constraints: [https://symfony.com/doc/current/validation.html#constraints](https://symfony.com/doc/current/validation.html#constraints)
