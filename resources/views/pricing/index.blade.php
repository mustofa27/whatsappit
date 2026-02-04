<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pricing - WAIt</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar-landing {
            background: transparent;
            padding: 1.5rem 0;
            transition: all 0.3s;
        }
        
        .navbar-landing.scrolled {
            background: rgba(255,255,255,0.95);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }
        
        .navbar-landing.scrolled .navbar-brand,
        .navbar-landing.scrolled .nav-link {
            color: #1e293b !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
        }
        
        .nav-link:hover {
            color: #fff !important;
        }
        
        /* Pricing Header */
        .pricing-hero {
            background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);
            padding: 6rem 0 4rem;
            position: relative;
            overflow: hidden;
        }
        
        .pricing-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .pricing-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .pricing-header {
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .pricing-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .pricing-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        /* Pricing Content */
        .pricing-content {
            background-color: #f8f9fa;
            padding: 4rem 0;
        }
        
        .pricing-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s;
            position: relative;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .pricing-card.popular {
            border: 3px solid #128C7E;
        }
        
        .popular-badge {
            position: absolute;
            top: -15px;
            right: 20px;
            background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .plan-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1e293b;
        }
        
        .plan-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #128C7E;
            margin-bottom: 0.5rem;
        }
        
        .plan-period {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }
        
        .feature-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list i {
            color: #10b981;
            font-size: 1.2rem;
        }
        
        .calculator-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-top: 3rem;
        }
        
        .calculator-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .calculator-header h2 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .cost-breakdown {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .cost-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .cost-item:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2rem;
            color: #128C7E;
        }
        
        .info-box {
            background: #e0e7ff;
            border-left: 4px solid #128C7E;
            padding: 1rem 1.5rem;
            border-radius: 5px;
            margin-top: 1.5rem;
        }
        
        .meta-pricing-table {
            margin-top: 2rem;
        }
        
        .meta-pricing-table table {
            width: 100%;
        }
        
        .meta-pricing-table th {
            background: #f8f9fa;
            padding: 1rem;
            font-weight: 600;
        }
        
        .meta-pricing-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        @media (max-width: 768px) {
            .pricing-header h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-whatsapp me-2"></i>WAIt
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pricing') }}">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#how-it-works">How It Works</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm ms-2" href="{{ route('register') }}">Get Started</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Pricing Hero Section -->
    <section class="pricing-hero">
        <div class="container pricing-header">
            <h1>Simple, Transparent Pricing</h1>
            <p>Choose the plan that fits your business needs</p>
        </div>
    </section>

    <!-- Pricing Content -->
    <section class="pricing-content">
        <div class="container pricing-container">
            <!-- Pricing Plans -->
            <div class="row g-4 mb-5">
                @foreach($plans as $plan)
                <div class="col-md-4">
                    <div class="pricing-card {{ $plan['popular'] ?? false ? 'popular' : '' }}">
                        @if($plan['popular'] ?? false)
                            <div class="popular-badge">Most Popular</div>
                        @endif
                        
                        <div class="plan-name">{{ $plan['name'] }}</div>
                        <div class="plan-price">Rp {{ number_format($plan['price'], 0, ',', '.') }}</div>
                        <div class="plan-period">per month</div>
                        
                        <ul class="feature-list">
                            @foreach($plan['features'] as $feature)
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                        
                        <a href="{{ route('register') }}" class="btn btn-primary w-100">
                            Get Started
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Cost Calculator -->
            <div class="calculator-section">
                <div class="calculator-header">
                    <h2><i class="bi bi-calculator me-2"></i>Cost Calculator</h2>
                    <p class="text-muted">Estimate your total monthly cost (Platform + Meta API)</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Platform Plan</label>
                            <select class="form-select" id="platformPlan">
                                @foreach($plans as $plan)
                                <option value="{{ $plan['price'] }}" {{ ($plan['popular'] ?? false) ? 'selected' : '' }}>
                                    {{ $plan['name'] }} - Rp {{ number_format($plan['price'], 0, ',', '.') }}/month
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Estimated Conversations per Month</label>
                            <input type="number" class="form-control" id="conversationCount" value="2000" min="0">
                            <small class="text-muted">First 1,000 conversations are FREE from Meta</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Message Category</label>
                            <select class="form-select" id="messageCategory">
                                <option value="marketing">Marketing (Rp {{ number_format($metaPricing['marketing'], 0, ',', '.') }}/conversation)</option>
                                <option value="utility" selected>Utility (Rp {{ number_format($metaPricing['utility'], 0, ',', '.') }}/conversation)</option>
                                <option value="service">Service (Rp {{ number_format($metaPricing['service'], 0, ',', '.') }}/conversation)</option>
                            </select>
                        </div>

                        <button class="btn btn-primary w-100" onclick="calculateCost()">
                            <i class="bi bi-calculator me-1"></i> Calculate Total Cost
                        </button>
                    </div>

                    <div class="col-md-6">
                        <div class="cost-breakdown">
                            <h5 class="mb-3">Cost Breakdown</h5>
                            
                            <div class="cost-item">
                                <span>Platform Subscription</span>
                                <span id="platformCost">Rp 1,500,000</span>
                            </div>
                            
                            <div class="cost-item">
                                <span>Total Conversations</span>
                                <span id="totalConversations">2,000</span>
                            </div>
                            
                            <div class="cost-item">
                                <span>Free Conversations (Meta)</span>
                                <span class="text-success">1,000</span>
                            </div>
                            
                            <div class="cost-item">
                                <span>Billable Conversations</span>
                                <span id="billableConversations">1,000</span>
                            </div>
                            
                            <div class="cost-item">
                                <span>Meta API Cost</span>
                                <span id="metaCost">Rp 700,000</span>
                            </div>
                            
                            <hr>
                            
                            <div class="cost-item">
                                <span>Total Monthly Cost</span>
                                <span id="totalCost">Rp 2,200,000</span>
                            </div>
                        </div>

                        <div class="info-box">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Important Notes</h6>
                            <ul class="mb-0 small">
                                <li><strong>Platform fee</strong> is paid to us (WAIt)</li>
                                <li><strong>Meta API cost</strong> is paid directly to Meta/Facebook</li>
                                <li>First 1,000 conversations per month are FREE from Meta</li>
                                <li>You need to add payment method in Meta Business Manager</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Meta Pricing Table -->
                <div class="meta-pricing-table">
                    <h5 class="mb-3">Meta WhatsApp API Pricing (Reference)</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Use Case</th>
                                <th>Cost per Conversation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Marketing</strong></td>
                                <td>Promotional messages, offers, campaigns</td>
                                <td>Rp {{ number_format($metaPricing['marketing'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Utility</strong></td>
                                <td>Account updates, order status, alerts</td>
                                <td>Rp {{ number_format($metaPricing['utility'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Service</strong></td>
                                <td>Customer service, support responses</td>
                                <td>Rp {{ number_format($metaPricing['service'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Authentication</strong></td>
                                <td>OTP, verification codes</td>
                                <td class="text-success fw-bold">FREE (limited)</td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="text-muted">* Prices are estimates in IDR. Actual Meta pricing may vary. Check Meta's official pricing for exact rates.</small>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);">
        <div class="container text-center text-white">
            <h2 class="mb-3" style="font-weight: 700;">Ready to Get Started?</h2>
            <p class="mb-4" style="font-size: 1.1rem; opacity: 0.9;">Join hundreds of developers using WAIt to power their messaging</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5" style="font-weight: 600; border-radius: 50px;">
                Create Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-whatsapp me-2"></i>WAIt</h5>
                    <p class="text-muted mb-0">WhatsApp API Service for Developers</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} WAIt. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const metaPricing = @json($metaPricing);

        // Navbar scroll effect (same as landing page)
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        function calculateCost() {
            const platformPrice = parseInt(document.getElementById('platformPlan').value);
            const conversations = parseInt(document.getElementById('conversationCount').value) || 0;
            const category = document.getElementById('messageCategory').value;

            const freeTier = metaPricing.free_tier;
            const billableConversations = Math.max(0, conversations - freeTier);
            const costPerConversation = metaPricing[category];
            const metaCost = billableConversations * costPerConversation;
            const totalCost = platformPrice + metaCost;

            // Update display
            document.getElementById('platformCost').textContent = 'Rp ' + platformPrice.toLocaleString('id-ID');
            document.getElementById('totalConversations').textContent = conversations.toLocaleString('id-ID');
            document.getElementById('billableConversations').textContent = billableConversations.toLocaleString('id-ID');
            document.getElementById('metaCost').textContent = 'Rp ' + metaCost.toLocaleString('id-ID');
            document.getElementById('totalCost').textContent = 'Rp ' + totalCost.toLocaleString('id-ID');
        }

        // Auto-calculate on page load
        calculateCost();

        // Auto-calculate on input change
        document.getElementById('platformPlan').addEventListener('change', calculateCost);
        document.getElementById('conversationCount').addEventListener('input', calculateCost);
        document.getElementById('messageCategory').addEventListener('change', calculateCost);
    </script>
</body>
</html>
