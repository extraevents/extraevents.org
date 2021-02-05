# Format for Competition

The specification defines the following types:

<table-of-contents>
### Simple example 
```json
{ 
  "id": "mpeiopen2019",
  "organizers": ["2015SOLO01","2015SOLO02"],
  "registration_close": "2021-12-31T00:00:00Z",
  "contact": "test@gmail.com",
  "events": [
    {
      "id": "222pyra",
      "round": 1,
      "format": "a",
      "time_limit": 60000,
      "competitor_limit": 12
    }
  ]
}
```

## Competition

Represents the root object.

| Attribute | Type | Description |
| --- | --- | --- |
| `id` | `String` | Competition identifier on the WCA. |
| `organizers` |  `[String]` | List of WCA ID of the organizers of extra events in the competition. |
| `registration_close` | `DateTime` | The point in time when the registering ends on the site ([ISO 8601](https://en.wikipedia.org/wiki/ISO_8601)) |
| `contact` | `String` | Email for contacts with organizers. |
| `events` | [`[Event]`](#event) | List of all events held at the competition. |

### Example

```json
{
  "organizers": ["2015SOLO01","2016SOLO02"],
  "registration_close": "2019-07-13T05:20:00+03:00",
  "contact": "test@gmail.com",
  "events": [...]
}
```

## Event

Represents data of an event held at the competition.

| Attribute | Type | Description |
| --- | --- | --- |
| `id` | `String` | The extra event identifier. |
| `round` | `Integer` | The round number. `1\|2\|3\|4` |
| `format` | `String` | The round format. `"1"\|"2"\|"3"\|"a"\|"m"` |
| `cutoff` | `Integer` | The cutoff in this round (in centiseconds). |
| `time_limit` | `Integer` | The time limit in this round (in centiseconds). |
| `time_limit_cumulative` | `Boolean` | Is the cumulative limit used. |
| `competitor_limit` | `Integer` | The competitors limit. |

### Example

```json
 {
    "id": 222pyra,
    "round": 1,
    "format": "a",
    "cutoff": 3000,
    "timeLimit": 60000,
    "isCumulative": false,
    "competitorLimit": 12
}
```

### Example

```json
{
  "type": "email",
  "value": "test@speedcubingextraevents.org"
}
```