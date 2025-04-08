FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Create necessary directories
RUN mkdir -p /var/www/html/php
RUN mkdir -p /var/www/html/php/test_researcher1
RUN mkdir -p /var/www/html/php/test_researcher1/test_study1_chat_logs/Group_1
RUN mkdir -p /var/www/html/php/test_researcher2

# Copy server-side PHP files
COPY ["server-side scripts/*.php", "/var/www/html/php/"]

# Create a simple index file
RUN echo '<?php echo "<h1>ETH DeSciL Smartriqs</h1>"; ?>' > /var/www/html/index.php

# Create test researcher data files with expected format
RUN echo 'Group ID,Condition,Group status,A,Last active,A#1,B,Last active,B#1' > /var/www/html/php/test_researcher1/test_study1_rawdata.csv && \
    echo '1,condition1,matched,test_participant1,1630000000,value1,test_participant2,1630000000,[.....]' >> /var/www/html/php/test_researcher1/test_study1_rawdata.csv && \
    echo '2,condition2,open,test_participant3,1630000000,value2,[open],[.....],[.....]' >> /var/www/html/php/test_researcher1/test_study1_rawdata.csv && \
    echo '3,condition1,matched,BOT 12345,1630000000,[DefaultResponse]bot_value,test_participant4,1630000000,value3' >> /var/www/html/php/test_researcher1/test_study1_rawdata.csv

# Create a test chat log file
RUN echo '&emsp;*** A has joined the chat ***<br>A (12:30): Hello!<br>B (12:31): Hi there!<br>' > /var/www/html/php/test_researcher1/test_study1_chat_logs/Group_1/default_log.htm

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

# Add startup message
CMD apache2-foreground
