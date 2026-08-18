# coachtech お問い合わせフォーム

## 概要

## ER図
```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email
        timestamp email_verified_at
        string password
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    categories ||--o{ contacts : "1つのカテゴリーは複数の問合せを持つ"
    categories {
        bigint id PK
        string content
        timestamp created_at
        timestamp updated_at
    }

    contacts ||--o{ contact_tag : "1つの問合せは複数のタグ関連を持つ"
    contacts {
        bigint id PK
        bigint category_id FK
        string first_name
        string last_name
        integer gender
        string email
        string tel
        string address
        string building
        text detail
        timestamp created_at
        timestamp updated_at
    }

    tags ||--o{ contact_tag : "1つのタグは複数のタグ関連を持つ"
    tags {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    contact_tag {
        bigint id PK
        bigint contact_id FK
        bigint tag_id FK
        timestamp created_at
        timestamp updated_at
    }
```
