
# Format for Extra Events Team

## Objects

The specification defines the following types:

<table-of-contents>

## Team

Represents the root object.

| Attribute | Type | Description |
| --- | --- | --- |
|  |  [`[Member]`](#member) | The member Extra Events Team. |

### Example

```json
[...]
```

## Member
Represents data of the member Extra Events Team .

| Attribute | Type | Description |
| --- | --- | --- |
| `id` | `String` | The WCA ID of the member. |
| `leader` | `Boolean` | Is the member a leader or not. |
| `description` | `String` | Description of activities (write "delete" for remove a member).|
| `contacts` | [`[Contact]`](#contact)| Lists contacts the member. |

### Example

```json
 {
    "id" : "2015SOLO01",
    "leader": true,
    "description": "Developer",
    "contacts": [...]
 }
```

## Contact

Represents the contact data of the member.

| Attribute | Type | Description |
| --- | --- | --- |
| `type` | `"email"\|"url"\|"phone"` | Type of contact. |
| `value` | `String` | The value of the contact. |

### Example

```json
{
  "type": "email",
  "value": "test@speedcubingextraevents.org"
}
```