# 🧠 Redis - Comandos Úteis e Dicas

Este documento contém comandos úteis para gerenciamento do Redis no projeto.

---

## 🧹 Limpar banco específico

Use quando quiser limpar apenas um database específico do Redis.

### Database 0
```bash
docker exec redis redis-cli -n 0 FLUSHDB
docker exec redis redis-cli -n 1 FLUSHDB
```

## 🧹 Limpar todos os bancos

```bash
docker exec redis redis-cli FLUSHALL
```
