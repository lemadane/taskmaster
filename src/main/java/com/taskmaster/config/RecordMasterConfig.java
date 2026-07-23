package com.taskmaster.config;

import io.succinct.recordmaster.RecordDatabase;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import jakarta.annotation.PreDestroy;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;

@Configuration
public class RecordMasterConfig {

    private RecordDatabase recordDatabase;

    @Bean
    public RecordDatabase recordDatabase(@Value("${recordmaster.db.path}") String dbPath) throws IOException {
        Path path = Path.of(dbPath);
        if (!Files.exists(path)) {
            Files.createDirectories(path);
        }
        this.recordDatabase = RecordDatabase.open(path);
        return this.recordDatabase;
    }

    @PreDestroy
    public void closeDatabase() {
        if (recordDatabase != null) {
            try {
                recordDatabase.close();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }
}
