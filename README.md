# NASIRA's (NAS) - Library Management System

A comprehensive web-based library management system that allows administrators to manage books and users, and enables users to search, borrow, and return books.

## Table of Contents
- [Features](#features)
- [Technical Implementation](#technical-implementation)
- [Database Structure](#database-structure)
- [Wireframes](#wireframes)
- [Limitations](#limitations)
- [Future Improvements](#future-improvements)
- [Acknowledgements](#acknowledgements)
- [Installation](#installation)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Features

### User Features

| Category | Features | Description |
|----------|----------|-------------|
| Authentication | Login System | Secure login with role-based access |
| | Session Management | Secure session handling |
| | Profile Management | Update personal information and password |
| Book Management | Search | Search by title, author, genre, subject |
| | View Details | Book information with cover images |
| | Borrowing | Request and track borrowed books |
| | History | View borrowing history |
| Dashboard | Overview | Personalized dashboard |
| | Recommendations | Book suggestions based on history |
| | Quick Access | Fast navigation to common features |

### Admin Features

| Category | Features | Description |
|----------|----------|-------------|
| User Management | Add Users | Create new user accounts |
| | View Users | List and manage all users |
| | Role Management | Assign and modify user roles |
| Book Management | Add Books | Add new books with details |
| | Update Books | Modify book information |
| | Inventory | Track book availability |
| Loan Management | Approve Requests | Process book requests |
| | Issue Books | Assign books to users |
| | Returns | Process book returns |
| | Overdue Tracking | Monitor overdue books |
| Dashboard | Statistics | Library usage analytics |
| | Charts | Visual data representation |
| | Quick Actions | Common admin tasks |

## Technical Implementation

### Frontend Technologies

| Technology | Version | Purpose |
|------------|---------|---------|
| Bootstrap | 5.x | UI Framework |
| Feather Icons | Latest | Icon Set |
| Material Design Icons | Latest | Additional Icons |
| C3.js | Latest | Data Visualization |
| Chart.js | Latest | Additional Charts |
| Custom CSS | - | Enhanced Styling |

### Backend Technologies

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.0+ | Server-side Language |
| MySQL | 5.7+ | Database |
| PHP Sessions | - | Session Management |
| PHP Mailer | Latest | Email Notifications |

### Security Features

| Feature | Implementation | Description |
|---------|---------------|-------------|
| Password Hashing | PHP password_hash() | Secure password storage |
| SQL Injection Prevention | Prepared Statements | Database security |
| XSS Protection | htmlspecialchars() | Output sanitization |
| Session Security | Custom Session Handler | Secure session management |
| Role-based Access | Custom Middleware | Access control |

## Database Structure

### Tables and Relationships

| Table | Primary Key | Foreign Keys | Description |
|-------|-------------|--------------|-------------|
| users | user_id | - | User information and credentials |
| books | book_id | genre_id, subject_id | Book details and inventory |
| loans | loan_id | user_id, book_id | Borrowing records |
| genres | genre_id | - | Book categories |
| subjects | subject_id | - | Subject categories |

## Wireframes

### User Interface Design

#### Login Page
![Login Page Wireframe](documentation/wireframes/login.png)

#### User Dashboard
![User Dashboard Wireframe](documentation/wireframes/user_dashboard.png)

#### Admin Dashboard
![Admin Dashboard Wireframe](documentation/wireframes/admin_dashboard.png)

#### Book Search
![Book Search Wireframe](documentation/wireframes/book_search.png)

#### Book Details
![Book Details Wireframe](documentation/wireframes/book_details.png)

## Limitations

### Functional Limitations

| Category | Limitation | Impact | Priority |
|----------|------------|--------|----------|
| User Management | No password reset | User inconvenience | High |
| | No email verification | Security risk | Medium |
| | No self-registration | Administrative overhead | Medium |
| Database | No cascading deletes | Data integrity issues | High |
| | Limited relationships | Complex queries required | Medium |
| Performance | External dependencies | Slow page loads | Medium |
| | No caching | Resource intensive | High |
| Email System | No batch processing | Manual email sending | Medium |
| | No queue system | Potential delays | Medium |

### Technical Limitations

| Category | Limitation | Impact | Workaround |
|----------|------------|--------|------------|
| Browser Support | Limited IE support | User restriction | Modern browsers only |
| Mobile Experience | Some features not optimized | Reduced usability | Responsive design |
| Scalability | No load balancing | Performance issues | Vertical scaling |

## Future Improvements

### Planned Enhancements

| Category | Improvement | Priority | Timeline |
|----------|-------------|----------|----------|
| User Experience | Password reset | High | Q2 2024 |
| | Email verification | Medium | Q3 2024 |
| | Self-registration | Medium | Q3 2024 |
| Performance | Caching system | High | Q2 2024 |
| | Query optimization | High | Q2 2024 |
| Security | 2FA implementation | High | Q3 2024 |
| | Rate limiting | Medium | Q4 2024 |
| Features | Book reviews | Low | Q4 2024 |
| | Reservations | Medium | Q3 2024 |

## Acknowledgements

### Design and Development

| Resource | Purpose | Version |
|----------|---------|---------|
| Bootstrap | UI Framework | 5.x |
| Figma | Design Tool | Latest |
| Dribbble | Design Inspiration | - |
| Behance | UI Patterns | - |
| Material Design Icons | Icon Set | Latest |
| Feather Icons | Additional Icons | Latest |

### Data Visualization

| Library | Purpose | Version |
|---------|---------|---------|
| C3.js | Charts | Latest |
| Chart.js | Additional Charts | Latest |

### Development Tools

| Tool | Purpose | Version |
|------|---------|---------|
| Git | Version Control | Latest |
| Composer | Dependency Management | Latest |
| PHPUnit | Testing | Latest |
| XDebug | Debugging | Latest |
| PHP_CodeSniffer | Code Style | Latest |

## Installation

### System Requirements

| Component | Version | Notes |
|-----------|---------|-------|
| PHP | 8.0+ | Required extensions: pdo, mysqli |
| MySQL | 5.7+ | InnoDB engine required |
| Web Server | Apache 2.4+ | mod_rewrite enabled |
| Composer | Latest | Dependency manager |

### Setup Instructions

1. **Clone Repository**
   ```bash
   git clone [repository-url]
   cd nas-library
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   ```bash
   mysql -u root -p < sql/schema.sql
   ```

4. **Configuration**
   - Update `config/database.php`
   - Configure `config/email.php`
   - Set up file upload settings

## Contributing

### Development Workflow

| Step | Action | Description |
|------|--------|-------------|
| 1 | Fork | Create personal fork |
| 2 | Branch | Create feature branch |
| 3 | Commit | Make changes |
| 4 | Push | Push to branch |
| 5 | PR | Create pull request |

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

| Role | Contact | Responsibility |
|------|---------|----------------|
| Project Manager | [email] | Overall project management |
| Lead Developer | [email] | Technical implementation |
| UI/UX Designer | [email] | Interface design |
| Database Admin | [email] | Database management |





