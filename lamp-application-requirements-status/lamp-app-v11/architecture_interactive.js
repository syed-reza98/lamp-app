/**
 * Enhanced Architecture Section JavaScript
 * Interactive enhancements for the AWS architecture diagram
 * Student: Anika Arman | ID: 14425754
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Architecture Section Enhancements...');

    // ========== INTERACTIVE ARCHITECTURE ENHANCEMENTS ========== //

    // 1. Service Box Interaction Effects
    const serviceBoxes = document.querySelectorAll('.service-box');
    serviceBoxes.forEach((box, index) => {
        // Add entrance animation with staggered delay
        if (box instanceof HTMLElement) {
            box.style.opacity = '0';
            box.style.transform = 'translateY(30px)';
        }
        
        setTimeout(() => {
            if (box instanceof HTMLElement) {
                box.style.transition = 'all 0.6s ease';
                box.style.opacity = '1';
                box.style.transform = 'translateY(0)';
            }
        }, index * 200);

        // Add hover sound effect simulation
        box.addEventListener('mouseenter', function() {
            if (this instanceof HTMLElement) {
                this.style.transform = 'perspective(1000px) rotateY(0deg) translateY(-10px) scale(1.03)';
                // Add ripple effect
                const ripple = document.createElement('div');
                ripple.className = 'ripple-effect';
                ripple.style.cssText = `
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 0;
                    height: 0;
                    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
                    border-radius: 50%;
                    transform: translate(-50%, -50%);
                    pointer-events: none;
                    animation: rippleExpand 0.6s ease-out;
                `;
                this.style.position = 'relative';
                this.appendChild(ripple);
                setTimeout(() => {
                    if (ripple.parentNode) {
                        ripple.parentNode.removeChild(ripple);
                    }
                }, 600);
            }
        });

        box.addEventListener('mouseleave', function() {
            if (this instanceof HTMLElement) {
                this.style.transform = 'perspective(1000px) rotateY(2deg)';
            }
        });
    });

    // 2. Requirement Items Interactive Checklist
    const reqItems = document.querySelectorAll('.req-item');
    reqItems.forEach((item, index) => {
        // Add progressive reveal animation
        if (item instanceof HTMLElement) {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-30px)';
        }
        
        setTimeout(() => {
            if (item instanceof HTMLElement) {
                item.style.transition = 'all 0.5s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }
        }, 1000 + (index * 100));

        // Add click interaction for requirement details
        item.addEventListener('click', function() {
            // Toggle expanded state
            const isExpanded = this.classList.contains('expanded');
            
            // Remove expanded class from all items
            reqItems.forEach(i => i.classList.remove('expanded'));
            
            if (!isExpanded) {
                this.classList.add('expanded');
                
                // Add detail popup effect
                const detail = document.createElement('div');
                detail.className = 'req-detail-popup';
                detail.innerHTML = `
                    <div class="popup-content">
                        <h4>Requirement ${this.querySelector('.req-letter')?.textContent ?? ''}</h4>
                        <p><strong>Service:</strong> ${this.querySelector('.req-name')?.textContent ?? ''}</p>
                        <p><strong>Status:</strong> ${this.querySelector('.req-status')?.textContent ?? ''}</p>
                        <p><strong>Implementation:</strong> Fully deployed and operational in AWS environment</p>
                        <button class="close-popup">×</button>
                    </div>
                `;
                detail.style.cssText = `
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) scale(0.8);
                    background: linear-gradient(135deg, rgba(20, 110, 180, 0.95) 0%, rgba(255, 153, 0, 0.95) 100%);
                    border-radius: 15px;
                    padding: 2rem;
                    color: white;
                    z-index: 1000;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                    backdrop-filter: blur(10px);
                    opacity: 0;
                    transition: all 0.3s ease;
                    max-width: 400px;
                    text-align: center;
                `;
                
                document.body.appendChild(detail);
                
                // Animate in
                setTimeout(() => {
                    detail.style.opacity = '1';
                    detail.style.transform = 'translate(-50%, -50%) scale(1)';
                }, 10);
                
                // Close popup functionality
                const closeBtn = detail.querySelector('.close-popup');
                if (closeBtn instanceof HTMLElement) {
                    closeBtn.style.cssText = `
                        position: absolute;
                        top: 10px;
                        right: 15px;
                        background: none;
                        border: none;
                        color: white;
                        font-size: 1.5rem;
                        cursor: pointer;
                        padding: 5px;
                    `;
                    const closePopup = () => {
                        detail.style.opacity = '0';
                        detail.style.transform = 'translate(-50%, -50%) scale(0.8)';
                        setTimeout(() => {
                            if (detail.parentNode) {
                                document.body.removeChild(detail);
                            }
                        }, 300);
                        this.classList.remove('expanded');
                    };
                    closeBtn.addEventListener('click', closePopup);
                    detail.addEventListener('click', (e) => {
                        if (e.target === detail) closePopup();
                    });
                    // Auto close after 5 seconds
                    setTimeout(closePopup, 5000);
                }
            }
        });

        // Add hover effects for requirement items
        item.addEventListener('mouseenter', function() {
            const letter = this.querySelector('.req-letter');
            if (letter instanceof HTMLElement) {
                letter.style.transform = 'scale(1.2) rotate(5deg)';
                letter.style.boxShadow = '0 8px 25px rgba(255, 153, 0, 0.6)';
            }
        });

        item.addEventListener('mouseleave', function() {
            const letter = this.querySelector('.req-letter');
            if (letter instanceof HTMLElement) {
                letter.style.transform = 'scale(1) rotate(0deg)';
                letter.style.boxShadow = '0 6px 20px rgba(255, 153, 0, 0.4)';
            }
        });
    });

    // 3. VPC Container Interactive Elements
    const vpcContainer = document.querySelector('.vpc-container');
    if (vpcContainer instanceof HTMLElement) {
        // Add network traffic simulation
        const createNetworkPulse = () => {
            const pulse = document.createElement('div');
            pulse.className = 'network-pulse';
            pulse.style.cssText = `
                position: absolute;
                width: 10px;
                height: 10px;
                background: radial-gradient(circle, #00ff88 0%, transparent 70%);
                border-radius: 50%;
                top: ${Math.random() * 80 + 10}%;
                left: ${Math.random() * 80 + 10}%;
                animation: networkFlow 3s linear forwards;
                pointer-events: none;
                z-index: 10;
            `;
            
            vpcContainer.style.position = 'relative';
            vpcContainer.appendChild(pulse);
            
            setTimeout(() => {
                if (pulse.parentNode) {
                    pulse.parentNode.removeChild(pulse);
                }
            }, 3000);
        };

        // Create network pulses periodically
        setInterval(createNetworkPulse, 2000);
    }

    // 4. Availability Zone Interaction
    const azones = document.querySelectorAll('.availability-zone');
    azones.forEach((az) => {
        az.addEventListener('click', function() {
            // Highlight the clicked AZ
            azones.forEach(zone => zone.classList.remove('az-highlighted'));
            this.classList.add('az-highlighted');
            
            // Show AZ details
            const azHeader = this.querySelector('.az-header');
            const azName = azHeader && azHeader.textContent ? azHeader.textContent : '';
            console.log(`Selected: ${azName}`);
            
            // Add visual feedback
            if (this instanceof HTMLElement) {
                this.style.boxShadow = '0 20px 40px rgba(255, 153, 0, 0.4), inset 0 0 30px rgba(255, 153, 0, 0.2)';
                setTimeout(() => {
                    this.style.boxShadow = '';
                    this.classList.remove('az-highlighted');
                }, 3000);
            }
        });
    });

    // 5. Connection Lines Animation Enhancement
    const connectionLines = document.querySelectorAll('.connection-line');
    connectionLines.forEach(line => {
        // Add data flow visualization
        const createDataFlow = () => {
            const dataPacket = document.createElement('div');
            dataPacket.style.cssText = `
                position: absolute;
                width: 8px;
                height: 8px;
                background: linear-gradient(45deg, #00ff88, #0099ff);
                border-radius: 50%;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                animation: dataFlow 2s linear infinite;
                box-shadow: 0 0 10px rgba(0, 255, 136, 0.7);
            `;
            if (line instanceof HTMLElement) {
                line.style.position = 'relative';
                line.appendChild(dataPacket);
            }
            setTimeout(() => {
                if (dataPacket.parentNode) {
                    dataPacket.parentNode.removeChild(dataPacket);
                }
            }, 2000);
        };

        // Start data flow animation
        setInterval(createDataFlow, 1500);
    });

    // 6. Architecture Container Parallax Effect
    const archContainer = document.querySelector('.architecture-container');
    if (archContainer instanceof HTMLElement) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const container = archContainer.getBoundingClientRect();
            const speed = scrolled * 0.1;
            
            if (container.top < window.innerHeight && container.bottom > 0) {
                archContainer.style.transform = `translateY(${speed}px)`;
            }
        });
    }

    // 7. Service Status Indicators
    const statusIndicators = document.querySelectorAll('.service-box');
    statusIndicators.forEach(service => {
        // Add status indicator dot
        const statusDot = document.createElement('div');
        statusDot.className = 'status-indicator';
        statusDot.style.cssText = `
            position: absolute;
            top: 15px;
            right: 15px;
            width: 12px;
            height: 12px;
            background: #00ff88;
            border-radius: 50%;
            animation: statusPulse 2s ease-in-out infinite;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.7);
        `;
        if (service instanceof HTMLElement) {
            service.style.position = 'relative';
            service.appendChild(statusDot);
        }
    });

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes rippleExpand {
            0% { width: 0; height: 0; opacity: 1; }
            100% { width: 100px; height: 100px; opacity: 0; }
        }
        
        @keyframes networkFlow {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.5); }
            100% { opacity: 0; transform: scale(2); }
        }
        
        @keyframes dataFlow {
            0% { top: 0; opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        
        @keyframes statusPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.2); }
        }
        
        .req-item.expanded {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25), 0 0 25px rgba(40, 167, 69, 0.3);
        }
        
        .az-highlighted {
            animation: azGlow 1s ease-in-out;
        }
        
        @keyframes azGlow {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.3); }
        }
    `;
    document.head.appendChild(style);

    console.log('Architecture Section Enhancements Initialized Successfully!');
});

// ========== ADDITIONAL UTILITY FUNCTIONS ========== //

// Function to simulate real-time updates
function simulateRealTimeUpdates() {
    const statusElements = document.querySelectorAll('.service-details span');
    
    setInterval(() => {
        statusElements.forEach(element => {
            if (
                element.textContent &&
                (element.textContent.includes('Status:') || element.textContent.includes('Health:'))
            ) {
                if (element instanceof HTMLElement) {
                    element.style.color = '#00ff88';
                    element.style.textShadow = '0 0 5px rgba(0, 255, 136, 0.5)';
                    setTimeout(() => {
                        element.style.color = '';
                        element.style.textShadow = '';
                    }, 1000);
                }
            }
        });
    }, 5000);
}

// Initialize real-time updates
setTimeout(simulateRealTimeUpdates, 2000);
