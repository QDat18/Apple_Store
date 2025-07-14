:root {
--primary-color: #1a1a1a;
--secondary-color: #2d3748;
--accent-color: #667eea;
--success-color: #48bb78;
--danger-color: #f56565;
--warning-color: #ed8936;
--info-color: #4299e1;
--background-color: #f8fafc;
--card-background: #ffffff;
--text-primary: #2d3748;
--text-secondary: #718096;
--border-color: #e2e8f0;
--shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
--shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
--border-radius: 12px;
--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
background-color: var(--background-color);
color: var(--text-primary);
line-height: 1.6;
}

.container {
max-width: 800px;
margin: 0 auto;
padding: 2rem 1rem;
text-align: center;
}

.success-message {
background: var(--card-background);
border-radius: var(--border-radius);
padding: 2rem;
box-shadow: var(--shadow-md);
margin-bottom: 2rem;
}

.success-title {
font-size: 1.8rem;
font-weight: 700;
color: var(--success-color);
margin-bottom: 1rem;
display: flex;
align-items: center;
justify-content: center;
gap: 0.5rem;
}

.success-details {
font-size: 1rem;
color: var(--text-secondary);
margin-bottom: 1.5rem;
}

.success-details p {
margin: 0.5rem 0;
}

.download-btn {
display: inline-flex;
align-items: center;
gap: 0.5rem;
padding: 0.75rem 1.5rem;
background: linear-gradient(135deg, var(--accent-color), var(--success-color));
color: white;
border: none;
border-radius: var(--border-radius);
font-size: 1rem;
font-weight: 600;
cursor: pointer;
transition: var(--transition);
margin: 0.5rem;
}

.download-btn:hover {
transform: translateY(-2px);
box-shadow: var(--shadow-lg);
}

.back-to-home {
display: inline-flex;
align-items: center;
gap: 0.5rem;
padding: 0.75rem 1.5rem;
background: none;
border: 2px solid var(--border-color);
color: var(--text-secondary);
border-radius: var(--border-radius);
font-size: 1rem;
cursor: pointer;
transition: var(--transition);
margin-top: 1rem;
}

.back-to-home:hover {
border-color: var(--accent-color);
color: var(--accent-color);
}

@media (max-width: 768px) {
.container {
padding: 1rem;
}

.success-title {
font-size: 1.5rem;
}

.download-btn,
.back-to-home {
width: 100%;
justify-content: center;
}
}